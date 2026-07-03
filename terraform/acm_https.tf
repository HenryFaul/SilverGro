# ─── HTTPS / ACM (custom domain) ─────────────────────────────────────────────
# Inert until you set `domain_name`. Two-phase because DNS is at an external
# registrar (validation records are added there by hand, not by Terraform):
#
#   Phase 1 — set `domain_name` and `terraform apply`. This requests the ACM
#             certificate and prints the CNAME validation records
#             (`terraform output acm_validation_records`). Add them at your
#             registrar and wait until the cert shows ISSUED in the ACM console.
#
#   Phase 2 — set `enable_https = true` and `terraform apply`. This adds the 443
#             listener with the cert, redirects 80 → 443, and flips APP_URL to
#             https://<domain>. Point your domain's record at the ALB DNS name.

variable "domain_name" {
  description = "Custom domain to serve the app on (e.g. app.example.com). Empty = HTTP only on the ALB DNS name."
  type        = string
  default     = ""
}

variable "enable_https" {
  description = "Create the HTTPS (443) listener + 80→443 redirect. Set true ONLY after the ACM cert for domain_name is ISSUED."
  type        = bool
  default     = false
}

locals {
  https_enabled = var.domain_name != "" && var.enable_https
}

resource "aws_acm_certificate" "app" {
  count             = var.domain_name != "" ? 1 : 0
  domain_name       = var.domain_name
  validation_method = "DNS"

  lifecycle {
    create_before_destroy = true
  }

  tags = { Name = "${local.name}-cert" }
}

resource "aws_lb_listener" "https" {
  count             = local.https_enabled ? 1 : 0
  load_balancer_arn = aws_lb.main.arn
  port              = 443
  protocol          = "HTTPS"
  ssl_policy        = "ELBSecurityPolicy-TLS13-1-2-2021-06"
  certificate_arn   = aws_acm_certificate.app[0].arn

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.app.arn
  }
}

output "acm_validation_records" {
  description = "CNAME records to add at your DNS registrar to validate the certificate (phase 1)."
  value = var.domain_name != "" ? [
    for o in aws_acm_certificate.app[0].domain_validation_options : {
      name  = o.resource_record_name
      type  = o.resource_record_type
      value = o.resource_record_value
    }
  ] : []
}
