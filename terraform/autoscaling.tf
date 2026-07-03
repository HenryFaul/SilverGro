# ─── Horizontal auto scaling (Application Auto Scaling) ──────────────────────
# Adds/removes ECS tasks behind the ALB based on average CPU and memory. Safe to
# run multiple tasks: sessions are stored in the database, not on the container.
# Extra tasks only run (and bill) while load keeps utilization above target.

variable "min_tasks" {
  description = "Minimum running tasks (always-on baseline capacity)."
  type        = number
  default     = 2
}

variable "max_tasks" {
  description = "Maximum tasks autoscaling may run under load."
  type        = number
  default     = 6
}

variable "cpu_target_percent" {
  description = "Target average CPU utilization (%). Above this, scale out."
  type        = number
  default     = 60
}

variable "memory_target_percent" {
  description = "Target average memory utilization (%). Above this, scale out."
  type        = number
  default     = 70
}

resource "aws_appautoscaling_target" "ecs" {
  max_capacity       = var.max_tasks
  min_capacity       = var.min_tasks
  resource_id        = "service/${aws_ecs_cluster.main.name}/${aws_ecs_service.app.name}"
  scalable_dimension = "ecs:service:DesiredCount"
  service_namespace  = "ecs"
}

resource "aws_appautoscaling_policy" "cpu" {
  name               = "${local.name}-cpu-target-tracking"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.ecs.resource_id
  scalable_dimension = aws_appautoscaling_target.ecs.scalable_dimension
  service_namespace  = aws_appautoscaling_target.ecs.service_namespace

  target_tracking_scaling_policy_configuration {
    predefined_metric_specification {
      predefined_metric_type = "ECSServiceAverageCPUUtilization"
    }
    target_value       = var.cpu_target_percent
    scale_out_cooldown = 60  # add capacity quickly under load
    scale_in_cooldown  = 300 # remove capacity slowly to avoid flapping
  }
}

resource "aws_appautoscaling_policy" "memory" {
  name               = "${local.name}-memory-target-tracking"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.ecs.resource_id
  scalable_dimension = aws_appautoscaling_target.ecs.scalable_dimension
  service_namespace  = aws_appautoscaling_target.ecs.service_namespace

  target_tracking_scaling_policy_configuration {
    predefined_metric_specification {
      predefined_metric_type = "ECSServiceAverageMemoryUtilization"
    }
    target_value       = var.memory_target_percent
    scale_out_cooldown = 60
    scale_in_cooldown  = 300
  }
}
