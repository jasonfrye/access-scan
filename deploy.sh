#!/bin/bash

# AccessScan Deployment Script
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production

set -e

ENV=${1:-production}
BRANCH=${2:-master}
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "🚀 Starting deployment for AccessScan ($ENV)"
echo "📁 Project directory: $PROJECT_DIR"
echo "🌿 Deploying branch: $BRANCH"

# Navigate to project directory
cd "$PROJECT_DIR"

# Pull latest changes
echo "📦 Pulling latest code..."
git fetch origin "$BRANCH"
git checkout "$BRANCH"
git pull origin "$BRANCH"

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --quiet

# Install NPM dependencies and build
echo "📦 Building assets..."
npm ci --quiet
npm run build

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed pricing configuration
echo "💰 Seeding pricing configuration..."
php artisan db:seed --class=PricingConfigSeeder --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan optimize:clear
php artisan optimize

# Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan horizon:terminate
php artisan horizon --once 2>/dev/null || true

echo ""
echo "✅ Deployment complete!"
echo ""
echo "Next steps:"
echo "  - Verify Horizon is running: php artisan horizon:status"
echo "  - Check application health: curl https://accessscan.app/api/health"
echo "  - Monitor queues at: https://accessscan.app/horizon"
