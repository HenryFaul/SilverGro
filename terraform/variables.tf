variable "aws_region" {
  description = "AWS region to deploy into."
  type        = string
  default     = "af-south-1"
}

variable "aws_profile" {
  description = "Named AWS CLI profile to authenticate with."
  type        = string
  default     = "SilverGro_AWS"
}

variable "deployment_id" {
  description = "Deployment identifier applied as a `Deployment` tag on every resource."
  type        = string
  default     = "silver_26"
}

variable "project" {
  description = "Project name, used as a prefix for resource names."
  type        = string
  default     = "silvergro"
}

variable "environment" {
  description = "Deployment environment (e.g. staging, production)."
  type        = string
  default     = "staging"
}

# ─── Networking ──────────────────────────────────────────────────────────────
variable "vpc_cidr" {
  description = "CIDR block for the VPC."
  type        = string
  default     = "10.20.0.0/16"
}

variable "az_count" {
  description = "Number of AZs / public subnets to spread across (min 2 for RDS subnet group)."
  type        = number
  default     = 2
}

# ─── Application container ────────────────────────────────────────────────────
variable "app_image_tag" {
  description = "Image tag to deploy from the ECR repository."
  type        = string
  default     = "latest"
}

variable "container_cpu" {
  description = "Fargate task CPU units (256 = 0.25 vCPU, 512 = 0.5 vCPU, 1024 = 1 vCPU)."
  type        = number
  default     = 512
}

variable "container_memory" {
  description = "Fargate task memory in MiB (per task). Valid with 0.5 vCPU: 1024-4096."
  type        = number
  default     = 2048
}

# Baseline/maximum task counts are controlled by min_tasks/max_tasks in
# autoscaling.tf (Application Auto Scaling manages the service's desired count).

# ─── Database (RDS MySQL) ─────────────────────────────────────────────────────
variable "db_engine_version" {
  description = "MySQL engine version."
  type        = string
  default     = "8.0"
}

variable "db_instance_class" {
  description = "RDS instance class."
  type        = string
  default     = "db.t4g.micro"
}

variable "db_allocated_storage" {
  description = "Allocated storage in GB."
  type        = number
  default     = 20
}

variable "db_name" {
  description = "Initial database name."
  type        = string
  default     = "silvergro"
}

variable "db_username" {
  description = "Master DB username."
  type        = string
  default     = "silvergro"
}

variable "db_multi_az" {
  description = "Run RDS in multi-AZ (costs more). Cost-optimized default is single-AZ."
  type        = bool
  default     = false
}

variable "db_backup_retention_days" {
  description = "Automated backup retention in days."
  type        = number
  default     = 7
}

variable "db_skip_final_snapshot" {
  description = "Skip the final snapshot on destroy (true is fine for staging)."
  type        = bool
  default     = true
}

variable "db_deletion_protection" {
  description = "Protect the RDS instance from accidental deletion."
  type        = bool
  default     = false
}

# ─── Media / S3 ───────────────────────────────────────────────────────────────
variable "media_bucket_public" {
  description = <<-EOT
    If true, uploaded media objects are readable via their direct public S3 URL
    (required for Spatie mediaLibrary getUrl() to work without code changes).
    SECURITY: this exposes any uploaded file to anyone who knows/guesses the URL.
    Leave false for a private bucket (then serve media via signed/temporary URLs).
  EOT
  type        = bool
  default     = false
}

# ─── Application secrets / config (runtime) ──────────────────────────────────
variable "app_env" {
  description = "Laravel APP_ENV."
  type        = string
  default     = "production"
}

variable "app_debug" {
  description = "Laravel APP_DEBUG."
  type        = bool
  default     = false
}

variable "app_url_override" {
  description = "Override APP_URL. Leave empty to use the ALB DNS name (http://...)."
  type        = string
  default     = ""
}

variable "mail_mailer" {
  type    = string
  default = "log"
}
variable "mail_host" {
  type    = string
  default = ""
}
variable "mail_port" {
  type    = string
  default = "587"
}
variable "mail_username" {
  type      = string
  default   = ""
  sensitive = true
}
variable "mail_password" {
  type      = string
  default   = ""
  sensitive = true
}
variable "mail_encryption" {
  type    = string
  default = "tls"
}
variable "mail_from_address" {
  type    = string
  default = "no-reply@example.com"
}
variable "mail_from_name" {
  type    = string
  default = "SilverGro"
}
