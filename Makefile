.PHONY: help dev-db-init

help:
	@echo "Available targets:"
	@echo "  dev-db-init    - Initialize local Postgres database for development"

dev-db-init:
	@./scripts/setup-postgres-dev.sh

