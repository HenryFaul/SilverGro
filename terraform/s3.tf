# Bucket for Spatie mediaLibrary / user uploads (FILESYSTEM_DISK=s3).
resource "aws_s3_bucket" "media" {
  bucket = "${local.name}-media"
}

resource "aws_s3_bucket_ownership_controls" "media" {
  bucket = aws_s3_bucket.media.id
  rule {
    object_ownership = "BucketOwnerEnforced"
  }
}

resource "aws_s3_bucket_public_access_block" "media" {
  bucket = aws_s3_bucket.media.id

  # When media_bucket_public is true we allow a public-read bucket policy so the
  # app's getUrl() links resolve. Otherwise everything stays locked down.
  block_public_acls       = true
  ignore_public_acls      = true
  block_public_policy     = !var.media_bucket_public
  restrict_public_buckets = !var.media_bucket_public
}

resource "aws_s3_bucket_cors_configuration" "media" {
  bucket = aws_s3_bucket.media.id

  cors_rule {
    allowed_headers = ["*"]
    allowed_methods = ["GET", "HEAD"]
    allowed_origins = ["*"]
    max_age_seconds = 3600
  }
}

# Optional public-read policy (only attached when media_bucket_public = true).
data "aws_iam_policy_document" "media_public" {
  count = var.media_bucket_public ? 1 : 0

  statement {
    sid       = "PublicReadObjects"
    effect    = "Allow"
    actions   = ["s3:GetObject"]
    resources = ["${aws_s3_bucket.media.arn}/*"]
    principals {
      type        = "*"
      identifiers = ["*"]
    }
  }
}

resource "aws_s3_bucket_policy" "media_public" {
  count      = var.media_bucket_public ? 1 : 0
  bucket     = aws_s3_bucket.media.id
  policy     = data.aws_iam_policy_document.media_public[0].json
  depends_on = [aws_s3_bucket_public_access_block.media]
}
