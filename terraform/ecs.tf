locals {
  app_url = local.https_enabled ? "https://${var.domain_name}" : (
    var.app_url_override != "" ? var.app_url_override : "http://${aws_lb.main.dns_name}"
  )

  # Non-sensitive environment variables passed to the container.
  container_environment = [
    { name = "APP_ENV", value = var.app_env },
    { name = "APP_DEBUG", value = tostring(var.app_debug) },
    { name = "APP_URL", value = local.app_url },
    { name = "ASSET_URL", value = local.app_url },
    { name = "LOG_CHANNEL", value = "stderr" },
    { name = "LOG_LEVEL", value = "info" },

    { name = "DB_CONNECTION", value = "mysql" },
    { name = "DB_HOST", value = aws_db_instance.main.address },
    { name = "DB_PORT", value = "3306" },
    { name = "DB_DATABASE", value = var.db_name },
    { name = "DB_USERNAME", value = var.db_username },

    { name = "SESSION_DRIVER", value = "database" },
    { name = "CACHE_DRIVER", value = "file" },
    { name = "QUEUE_CONNECTION", value = "sync" },
    { name = "BROADCAST_DRIVER", value = "log" },

    # File storage on S3 (credentials come from the task IAM role, not env keys).
    { name = "FILESYSTEM_DISK", value = "s3" },
    { name = "AWS_BUCKET", value = aws_s3_bucket.media.bucket },
    { name = "AWS_DEFAULT_REGION", value = var.aws_region },
    { name = "AWS_USE_PATH_STYLE_ENDPOINT", value = "false" },

    { name = "MAIL_MAILER", value = var.mail_mailer },
    { name = "MAIL_HOST", value = var.mail_host },
    { name = "MAIL_PORT", value = var.mail_port },
    { name = "MAIL_ENCRYPTION", value = var.mail_encryption },
    { name = "MAIL_FROM_ADDRESS", value = var.mail_from_address },
    { name = "MAIL_FROM_NAME", value = var.mail_from_name },

    { name = "INERTIA_SSR_ENABLED", value = "false" },
  ]

  container_secrets = [
    { name = "APP_KEY", valueFrom = aws_ssm_parameter.app_key.arn },
    { name = "DB_PASSWORD", valueFrom = aws_ssm_parameter.db_password.arn },
    { name = "MAIL_USERNAME", valueFrom = aws_ssm_parameter.mail_username.arn },
    { name = "MAIL_PASSWORD", valueFrom = aws_ssm_parameter.mail_password.arn },
  ]
}

resource "aws_ecs_cluster" "main" {
  name = local.name

  setting {
    name  = "containerInsights"
    value = "disabled" # cost-optimized; enable for richer metrics
  }
}

resource "aws_cloudwatch_log_group" "app" {
  name              = "/ecs/${local.name}"
  retention_in_days = 30
}

resource "aws_ecs_task_definition" "app" {
  family                   = local.name
  requires_compatibilities = ["FARGATE"]
  network_mode             = "awsvpc"
  cpu                      = var.container_cpu
  memory                   = var.container_memory
  execution_role_arn       = aws_iam_role.task_execution.arn
  task_role_arn            = aws_iam_role.task.arn

  runtime_platform {
    operating_system_family = "LINUX"
    # ARM64/Graviton on Fargate: ~20% cheaper and better price/performance,
    # consistent with the Graviton (t4g) RDS instance. The image is built for
    # linux/arm64 in CI (native ARM64 GitHub runner) and by deploy.sh.
    cpu_architecture = "ARM64"
  }

  container_definitions = jsonencode([
    {
      name        = "app"
      image       = "${aws_ecr_repository.app.repository_url}:${var.app_image_tag}"
      essential   = true
      environment = local.container_environment
      secrets     = local.container_secrets

      portMappings = [
        { containerPort = 80, protocol = "tcp" }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = aws_cloudwatch_log_group.app.name
          "awslogs-region"        = var.aws_region
          "awslogs-stream-prefix" = "app"
        }
      }
    }
  ])
}

resource "aws_ecs_service" "app" {
  name            = local.name
  cluster         = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.app.arn
  desired_count   = var.min_tasks # initial baseline; autoscaling manages it thereafter
  launch_type     = "FARGATE"

  enable_execute_command = true

  network_configuration {
    subnets          = aws_subnet.public[*].id
    security_groups  = [aws_security_group.ecs.id]
    assign_public_ip = true # required to pull image / reach AWS APIs without NAT
  }

  load_balancer {
    target_group_arn = aws_lb_target_group.app.arn
    container_name   = "app"
    container_port   = 80
  }

  # Give a new task time to warm up before the LB counts it healthy.
  health_check_grace_period_seconds = 60

  deployment_minimum_healthy_percent = 100
  deployment_maximum_percent         = 200

  # The image tag/task-def is updated out-of-band by deploy.sh; don't let
  # terraform fight those deploys on desired_count.
  lifecycle {
    ignore_changes = [desired_count]
  }

  depends_on = [aws_lb_listener.http]
}
