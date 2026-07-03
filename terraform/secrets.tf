# ─── Generated secrets ───────────────────────────────────────────────────────
resource "random_password" "db" {
  length  = 32
  special = false # keep DB password alnum to avoid URL/CLI escaping issues
}

# Laravel APP_KEY = "base64:" + 32 random bytes, base64-encoded.
resource "random_id" "app_key" {
  byte_length = 32
}

locals {
  app_key = "base64:${random_id.app_key.b64_std}"
}

# ─── SSM Parameter Store (SecureString) ──────────────────────────────────────
# Runtime secrets injected into the container via ECS `secrets`. Stored encrypted
# with the AWS-managed SSM KMS key.
resource "aws_ssm_parameter" "app_key" {
  name  = "/${local.name}/APP_KEY"
  type  = "SecureString"
  value = local.app_key
}

resource "aws_ssm_parameter" "db_password" {
  name  = "/${local.name}/DB_PASSWORD"
  type  = "SecureString"
  value = random_password.db.result
}

resource "aws_ssm_parameter" "mail_username" {
  name  = "/${local.name}/MAIL_USERNAME"
  type  = "SecureString"
  value = var.mail_username != "" ? var.mail_username : "unset"
}

resource "aws_ssm_parameter" "mail_password" {
  name  = "/${local.name}/MAIL_PASSWORD"
  type  = "SecureString"
  value = var.mail_password != "" ? var.mail_password : "unset"
}
