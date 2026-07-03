# SilverGro — AWS ECS + RDS Deployment

Deploys the Dockerized Laravel/Vue app to **AWS Fargate (ECS)** behind an **ALB**,
with **RDS MySQL 8** and an **S3** bucket for uploads. Cost-optimized posture:
single-AZ RDS, public subnets, **no NAT gateway**, 1 task.

## Architecture

```
Internet ──▶ ALB (HTTP :80) ──▶ Fargate task (nginx + php-fpm, 1 container)
                                      │  ├─▶ RDS MySQL (private, SG-locked)
                                      │  └─▶ S3 media bucket (via task IAM role)
                                      └─ logs ─▶ CloudWatch (/ecs/silvergro-<env>)
Secrets (APP_KEY, DB_PASSWORD, mail) ─▶ SSM Parameter Store (SecureString)
```

The container image is built from [`Dockerfile.production`](../Dockerfile.production)
— a self-contained image (assets + composer deps baked in), unlike the dev
`docker-compose.yml`.

## What Terraform creates

VPC + 2 public subnets/IGW · security groups · ECR repo · RDS MySQL · S3 bucket ·
SSM secrets (auto-generated `APP_KEY` + DB password) · IAM roles · ALB + target
group + listener · ECS cluster, task definition & service.

## Prerequisites

- Terraform ≥ 1.5, AWS CLI v2, Docker (with buildx for `linux/amd64`)
- AWS credentials with permission to create the above
- **af-south-1 must be enabled** in the account (it's an opt-in region:
  Account → *AWS Regions* → enable *Africa (Cape Town)*)

## First deploy

```bash
# 1. Configure credentials (any standard method works)
export AWS_ACCESS_KEY_ID=...
export AWS_SECRET_ACCESS_KEY=...
export AWS_DEFAULT_REGION=af-south-1

# 2. Provision infrastructure
cd terraform
cp terraform.tfvars.example terraform.tfvars   # edit if needed
terraform init
terraform apply                                 # creates everything incl. an empty ECR repo

# 3. Build, push, migrate and deploy the app image
cd ..
# Optional: bake the Google Maps key into the frontend bundle
export VITE_GOOGLE_MAPS_API_KEY=your-key
./scripts/deploy.sh

# 4. Open the app
terraform -chdir=terraform output -raw app_url
```

The very first `terraform apply` starts the ECS service pointing at an image tag
that doesn't exist yet, so the first task can't start until `deploy.sh` pushes the
image — this is expected. After `deploy.sh` runs, the service stabilizes.

## Redeploying after code changes

Two ways to deploy — both do the same thing (build → push → migrate → roll out):

### A. GitHub Actions (push to `master`)

[`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml) builds the image
on GitHub's runners and deploys automatically on every push to `master` (or via
the Actions tab → *Run workflow*). Auth is keyless via **GitHub OIDC** — no AWS
keys are stored in GitHub. The IAM role it assumes is created in
[`github_oidc.tf`](github_oidc.tf) (`terraform output github_actions_role_arn`).

- Trigger branch is `master` (change `github_deploy_branch` + the workflow's
  `on.push.branches` to adjust). The workflow file must exist **on master** for it
  to run, so merge it there first.
- To bake the Google Maps key into the bundle, add it as a repo secret:
  `gh secret set VITE_GOOGLE_MAPS_API_KEY --repo HenryFaul/SilverGro`.

### B. Local (`./scripts/deploy.sh`)

Builds on your machine (cross-compiles `linux/amd64`), pushes `:latest`, and
deploys. Handy for hotfixes or when you don't want to push to `master`.

Both run `php artisan migrate --force` as a **one-off Fargate task** (migrations
are deliberately *not* run on web-container startup), then force a new service
deployment and wait for it to stabilize.

## Configuration knobs

Set these in `terraform/terraform.tfvars`:

| Variable | Default | Notes |
|---|---|---|
| `container_cpu` / `container_memory` | 512 / 1024 | 0.5 vCPU / 1 GB |
| `desired_count` | 1 | number of web tasks |
| `db_instance_class` | `db.t4g.micro` | Graviton, cheapest MySQL |
| `db_multi_az` | `false` | set `true` for failover |
| `media_bucket_public` | `false` | see **Media storage** below |
| `app_url_override` | `""` | set to your `https://` domain once TLS is added |
| `mail_*` | `log` mailer | configure SES/SMTP for real email |

## Media storage (important)

Uploads go to S3 (`FILESYSTEM_DISK=s3`). The bucket is **private by default**.

- Spatie mediaLibrary `getUrl()` returns a **direct** S3 URL, which **403s on a
  private bucket**. To make those links work without code changes, set
  `media_bucket_public = true` — but that makes every uploaded file publicly
  readable to anyone with the URL.
- The secure alternative is to keep the bucket private and change the app to use
  temporary/signed URLs (`getTemporaryUrl()` / `Storage::temporaryUrl()`), or put
  CloudFront with signed URLs in front. Recommended for anything sensitive
  (deal attachments, etc.).

Decide before going live.

## Adding a custom domain + HTTPS

The HTTPS wiring is already in [`acm_https.tf`](acm_https.tf) — inert until you set
`domain_name`. DNS is at an external registrar, so it's a two-phase apply:

**Phase 1 — request the certificate**

```bash
# in terraform/terraform.tfvars
domain_name = "app.yourdomain.co.za"
```
```bash
terraform apply
terraform output acm_validation_records   # add these CNAMEs at your registrar
```
Wait until the certificate shows **Issued** in the ACM console (af-south-1).

**Phase 2 — turn on HTTPS**

```bash
# in terraform/terraform.tfvars
enable_https = true
```
```bash
terraform apply
```
This adds the 443 listener with the cert, redirects 80 → 443, and sets
`APP_URL=https://<domain>`. Finally, point your domain at the ALB:

```
CNAME  app.yourdomain.co.za  →  <alb_dns_name output>
```
(or an ALIAS/ANAME at the apex). `TrustProxies` already trusts the ALB, so Laravel
detects HTTPS correctly. Note: an app redeploy isn't required — `terraform apply`
updates the running task's `APP_URL` env on the next deployment; run the workflow
(or `deploy.sh`) if you want it applied immediately.

> **Why not HTTPS on the ELB domain?** A public CA (incl. ACM) will only issue a
> cert for a domain you control. The `*.elb.amazonaws.com` name belongs to AWS, so
> it can't get a trusted cert — a custom domain is required.

## Operations

```bash
# Tail application logs
aws logs tail /ecs/silvergro-staging --follow --region af-south-1

# Shell into the running task (ECS Exec is enabled)
aws ecs execute-command --cluster silvergro-staging \
  --task <task-id> --container app --interactive --command "/bin/sh" \
  --region af-south-1

# Run an arbitrary artisan command as a one-off task (e.g. db:seed)
# — same pattern deploy.sh uses for migrations.
```

## Teardown

```bash
cd terraform
terraform destroy
```

`force_delete` is on for ECR, and `db_skip_final_snapshot`/`deletion_protection`
default to staging-friendly values — review those before using in production.

## Cost (rough, af-south-1)

Fargate 0.5vCPU/1GB (1 task, 24/7) + `db.t4g.micro` single-AZ + ALB + storage
lands roughly in the **~$55–75/month** range. Biggest lever: the ALB (~$18/mo)
and Fargate. There is intentionally **no NAT gateway** (~$32/mo saved).
