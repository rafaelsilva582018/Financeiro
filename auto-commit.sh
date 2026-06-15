#!/bin/bash

cd /var/www/Financeiro || exit 1

# Verifica se houve alteração
if [[ -n $(git status --porcelain) ]]; then
  git add .
  git commit -m "auto: update $(date '+%Y-%m-%d %H:%M:%S')"
  git push origin main
fi
