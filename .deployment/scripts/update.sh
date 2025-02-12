#!/usr/bin/env bash
current_dir=$(basename "$PWD")
image_name="${current_dir}-web"
# Concatenate the directory name and container
docker compose -f docker-compose-prod.yml down
docker image rm $image_name
docker compose -f docker-compose-prod.yml up -d --build
