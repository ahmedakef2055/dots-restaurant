#!/bin/bash
set -e

echo ""
echo "============================================"
echo "  Dots - Restaurant Management System"
echo "  Deploy Script for dots.systemco.me"
echo "============================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

step() { echo -e "${GREEN}[+]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }

# ── 1. Composer ──────────────────────────────────────────────
step "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --quiet

# ── 2. .env ──────────────────────────────────────────────────
if [ ! -f .env ]; then
    step "Creating .env file..."
    cp .env.example .env

    # Ask for DB credentials
    echo ""
    warn "Enter your cPanel database details:"
    read -p "  DB Host (default: localhost): " DB_HOST
    DB_HOST=${DB_HOST:-localhost}

    read -p "  DB Name: " DB_DATABASE
    read -p "  DB Username: " DB_USERNAME
    read -s -p "  DB Password: " DB_PASSWORD
    echo ""

    sed -i "s|APP_URL=.*|APP_URL=https://dots.systemco.me|" .env
    sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
    sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST}|" .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env

    step "Generating app key..."
    php artisan key:generate --quiet
else
    warn ".env already exists — skipping DB setup."
fi

# ── 3. Storage ───────────────────────────────────────────────
step "Setting up storage link..."
php artisan storage:link --quiet 2>/dev/null || true

# ── 4. Permissions ───────────────────────────────────────────
step "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# ── 5. Migrations ────────────────────────────────────────────
step "Running database migrations..."
php artisan migrate --force --quiet

# ── 6. Cache ─────────────────────────────────────────────────
step "Caching config, routes, views..."
php artisan config:cache --quiet
php artisan route:cache --quiet
php artisan view:cache --quiet

# ── Done ─────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Deploy complete!${NC}"
echo -e "${GREEN}  Site: https://dots.systemco.me${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
warn "Make sure Document Root in cPanel points to:"
echo "     $(pwd)/public"
echo ""
