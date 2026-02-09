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

# Setup systemd service (if deploying to server)
if [ "$ENV" = "production" ] && [ -f "deployment/accessscan-worker.service" ]; then
    echo "⚙️  Setting up systemd service..."
    if command -v systemctl &> /dev/null; then
        # Copy service file if deploying to standard location
        if [ -d "/etc/systemd/system" ]; then
            sudo cp deployment/accessscan-worker.service /etc/systemd/system/accessscan-worker.service
            sudo systemctl daemon-reload
            sudo systemctl enable accessscan-worker 2>/dev/null || true
            echo "✅ Systemd service installed"
        else
            echo "⚠️  systemctl not available - skipping systemd setup"
            echo "   Copy deployment/accessscan-worker.service manually"
        fi
    else
        echo "⚠️  systemctl not found - skipping systemd setup"
    fi

    # Setup cron (if deploying to server)
    if command -v crontab &> /dev/null; then
        echo "⚙️  Setting up scheduled tasks..."
        # Add to crontab if not already present
        CRON_JOB="* * * * * www-data cd $PROJECT_DIR && php artisan schedule:run >> /dev/null 2>&1"
        if ! crontab -l 2>/dev/null | grep -q "schedule:run"; then
            (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
            echo "✅ Cron job added"
        else
            echo "ℹ️  Cron job already exists"
        fi
    fi
fi

echo ""
echo "✅ Deployment complete!"
echo ""
echo "🚀 Queue worker running: sudo systemctl status accessscan-worker"
echo "📊 Horizon dashboard: https://accessscan.app/horizon"
echo "❤️  Health check: curl https://accessscan.app/api/health"
