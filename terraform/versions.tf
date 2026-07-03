terraform {
  required_version = ">= 1.5.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.60"
    }
    random = {
      source  = "hashicorp/random"
      version = "~> 3.6"
    }
  }

  # For a team setup, move state to S3 + DynamoDB by filling this in and
  # running `terraform init -migrate-state`. Left as local state by default.
  # backend "s3" {
  #   bucket         = "silvergro-tf-state"
  #   key            = "ecs/terraform.tfstate"
  #   region         = "af-south-1"
  #   dynamodb_table = "silvergro-tf-locks"
  #   encrypt        = true
  # }
}

provider "aws" {
  region  = var.aws_region
  profile = var.aws_profile

  default_tags {
    tags = {
      Project     = var.project
      Environment = var.environment
      Deployment  = var.deployment_id
      ManagedBy   = "terraform"
    }
  }
}
