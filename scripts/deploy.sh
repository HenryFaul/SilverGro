#!/usr/bin/env bash
#
# Build → push → migrate → deploy the SilverGro app to ECS.
#
# Prereqs:
#   - `terraform apply` has been run in ./terraform (infra exists)
#   - AWS CLI + Docker installed and AWS credentials configured
#   - Run from the repo root:  ./scripts/deploy.sh
#
# Optional env vars:
#   IMAGE_TAG                  image tag to push/deploy (default: latest)
#   VITE_GOOGLE_MAPS_API_KEY   baked into the JS bundle at build time
#
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TF_DIR="${REPO_ROOT}/terraform"
IMAGE_TAG="${IMAGE_TAG:-latest}"

# Use the SilverGro AWS profile for every aws CLI call unless overridden.
export AWS_PROFILE="${AWS_PROFILE:-SilverGro_AWS}"

echo "==> Reading Terraform outputs"
tf() { terraform -chdir="${TF_DIR}" output -raw "$1"; }

REGION="$(tf aws_region)"
ECR_URL="$(tf ecr_repository_url)"
CLUSTER="$(tf ecs_cluster_name)"
SERVICE="$(tf ecs_service_name)"
TASK_FAMILY="$(tf task_definition_family)"
SG="$(tf ecs_security_group_id)"
SUBNETS="$(terraform -chdir="${TF_DIR}" output -json private_subnet_ids | tr -d '[]" ' )"

echo "    region=${REGION} cluster=${CLUSTER} service=${SERVICE}"

# ─── 1. Build ────────────────────────────────────────────────────────────────
echo "==> Building image (linux/amd64) ${ECR_URL}:${IMAGE_TAG}"
docker build \
  --platform linux/amd64 \
  -f "${REPO_ROOT}/Dockerfile.production" \
  --build-arg VITE_GOOGLE_MAPS_API_KEY="${VITE_GOOGLE_MAPS_API_KEY:-}" \
  -t "${ECR_URL}:${IMAGE_TAG}" \
  "${REPO_ROOT}"

# ─── 2. Push ─────────────────────────────────────────────────────────────────
echo "==> Logging in to ECR and pushing"
aws ecr get-login-password --region "${REGION}" \
  | docker login --username AWS --password-stdin "${ECR_URL%/*}"
docker push "${ECR_URL}:${IMAGE_TAG}"

# ─── 3. Run migrations as a one-off task ─────────────────────────────────────
echo "==> Running database migrations (one-off Fargate task)"
NETWORK_CONFIG="awsvpcConfiguration={subnets=[${SUBNETS}],securityGroups=[${SG}],assignPublicIp=ENABLED}"
OVERRIDES='{"containerOverrides":[{"name":"app","command":["php","artisan","migrate","--force"]}]}'

TASK_ARN="$(aws ecs run-task \
  --cluster "${CLUSTER}" \
  --launch-type FARGATE \
  --task-definition "${TASK_FAMILY}" \
  --network-configuration "${NETWORK_CONFIG}" \
  --overrides "${OVERRIDES}" \
  --started-by "deploy-script" \
  --region "${REGION}" \
  --query 'tasks[0].taskArn' --output text)"

echo "    migration task: ${TASK_ARN}"
echo "    waiting for it to finish..."
aws ecs wait tasks-stopped --cluster "${CLUSTER}" --tasks "${TASK_ARN}" --region "${REGION}"

EXIT_CODE="$(aws ecs describe-tasks --cluster "${CLUSTER}" --tasks "${TASK_ARN}" --region "${REGION}" \
  --query 'tasks[0].containers[0].exitCode' --output text)"

if [ "${EXIT_CODE}" != "0" ]; then
  echo "!! Migration task exited with code ${EXIT_CODE}. Check CloudWatch logs (/ecs/...) before proceeding." >&2
  exit 1
fi
echo "    migrations OK"

# ─── 4. Roll out the new image to the service ────────────────────────────────
echo "==> Forcing a new service deployment"
aws ecs update-service \
  --cluster "${CLUSTER}" \
  --service "${SERVICE}" \
  --force-new-deployment \
  --region "${REGION}" \
  --query 'service.deployments[0].{status:rolloutState,desired:desiredCount}' --output table

echo "==> Waiting for the service to stabilize..."
aws ecs wait services-stable --cluster "${CLUSTER}" --services "${SERVICE}" --region "${REGION}"

echo "==> Done. App URL:"
tf app_url; echo
