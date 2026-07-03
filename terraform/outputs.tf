output "app_url" {
  description = "Public URL of the application (ALB DNS name until a custom domain is added)."
  value       = local.app_url
}

output "alb_dns_name" {
  description = "ALB DNS name."
  value       = aws_lb.main.dns_name
}

output "ecr_repository_url" {
  description = "ECR repository to push the app image to."
  value       = aws_ecr_repository.app.repository_url
}

output "ecs_cluster_name" {
  value = aws_ecs_cluster.main.name
}

output "ecs_service_name" {
  value = aws_ecs_service.app.name
}

output "task_definition_family" {
  value = aws_ecs_task_definition.app.family
}

output "rds_endpoint" {
  description = "RDS endpoint (host)."
  value       = aws_db_instance.main.address
}

output "s3_media_bucket" {
  value = aws_s3_bucket.media.bucket
}

output "aws_region" {
  value = var.aws_region
}

# Network config for one-off tasks (e.g. migrations) run via the AWS CLI.
output "private_subnet_ids" {
  description = "Subnets for run-task network configuration."
  value       = aws_subnet.public[*].id
}

output "ecs_security_group_id" {
  value = aws_security_group.ecs.id
}
