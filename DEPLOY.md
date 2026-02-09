# Deployment Guide

## Quick Deploy to Laravel Forge

### One-Click Setup
1. Connect your GitHub repository to Laravel Forge
2. Create a new server (recommended: DigitalOcean or AWS)
3. Forge will automatically:
   - Install PHP, Nginx, MySQL/PostgreSQL
   - Set up Let's Encrypt SSL
   - Configure queue workers

### Environment Variables
Copy this to your Forge "Environment" tab:

```env
APP_NAME="AccessScan"
APP_ENV=production
APP_KEY=base64:xxxxx
APP_URL=https://accessscan.app

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=access_scan
DB_USERNAME=forge
DB_PASSWORD=xxxxx

# Redis (for Horizon queue)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Stripe
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
STRIPE_PRICE_MONTHLY=price_xxxxx
STRIPE_PRICE_LIFETIME=price_xxxxx

# Mail (Mailgun recommended)
MAIL_MAILER=mailgun
MAILGUN_API_KEY=key-xxxxx
MAILGUN_DOMAIN=mg.accessscan.app
MAILGUN_ENDPOINT=api.mailgun.net
MAILGUN_VERSION=v3
MAIL_FROM_ADDRESS=noreply@accessscan.app
MAIL_FROM_NAME="AccessScan"

# App
PA11Y_CLI_VERSION=6
SCAN_TIMEOUT=300
MAX_PAGES_PER_SCAN=100
```

### Queue Workers (Forge)
In Forge "Queue" tab, configure:
- Command: `php artisan horizon`
- User: www-data
- Processes: 2
- Max Tasks: 1000
- Sleep: 3
- Max Time: 3600

### Scheduled Tasks (Forge)
Add this in "Scheduler" tab:
```
* * * * * cd /home/forge/accessscan.app && php artisan schedule:run >> /dev/null 2>&1
```

This runs:
- Daily usage resets at midnight
- Trial expiration checks at 9 AM
- Weekly digests on Mondays at 10 AM
- Regression alerts for scheduled scans

### Storage
Forge automatically runs `php artisan storage:link`. If not:
```bash
php artisan storage:link
```

### NPM Build
Forge will run `npm install && npm run build` automatically on deploy.

### Database Migrations
Add a "Deployment Hook" in Forge "Hooks" tab:
```bash
cd /home/forge/accessscan.app
php artisan migrate --force
php artisan db:seed --class=PricingConfigSeeder
```

## Manual Deployment

### Prerequisites
- PHP 8.2+
- Composer 2+
- Node.js 20+
- MySQL 8.0+ or PostgreSQL
- Redis 7+
- Pa11y CLI: `npm install -g pa11y@6`

### Steps

```bash
# Clone and install
git clone git@github.com:jasonfrye/access-scan.git
cd access-scan
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=PricingConfigSeeder

# Setup queue worker (systemd)
sudo nano /etc/systemd/system/accessscan-worker.service
# (see systemd section below)

# Start services
sudo systemctl enable accessscan-worker
sudo systemctl start accessscan-worker

# Configure cron
echo "* * * * * cd /path/to/access-scan && php artisan schedule:run >> /dev/null 2>&1" | sudo crontab -
```

### Systemd Service
Create `/etc/systemd/system/accessscan-worker.service`:

```ini
[Unit]
Description=AccessScan Horizon Worker
After=network.target

[Service]
Type=forking
User=www-data
Group=www-data
Restart=always
RestartSec=5
ExecStart=/usr/bin/php /var/www/access-scan/current/artisan horizon
ExecStop=/usr/bin/php /var/www/access-scan/current/artisan horizon:terminate
WorkingDirectory=/var/www/access-scan/current
StandardOutput=append:/var/log/accessscan-worker.log
StandardError=append:/var/log/accessscan-worker-error.log

[Install]
WantedBy=multi-user.target
```

### Nginx Configuration
Ensure these are set:
```nginx
location ~ \.php$ {
    try_files $uri =404;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location /horizon {
    auth_basic "Admin";
    auth_basic_user_file /etc/nginx/htpasswd;
}
```

## Post-Deploy Checklist

- [ ] SSL certificate installed (Let's Encrypt auto-renewal enabled)
- [ ] `APP_DEBUG=false` in production
- [ ] Queue worker running (`php artisan horizon`)
- [ ] Scheduler cron configured
- [ ] Test scan works: `curl -X POST https://accessscan.app/scan -d url=https://example.com`
- [ ] Check Horizon dashboard at `/horizon` (password protected)
- [ ] Verify emails send (check logs or Mailgun dashboard)
- [ ] Stripe webhook endpoint configured: `https://accessscan.app/stripe/webhook`
- [ ] Test payment flow in Stripe test mode

## Troubleshooting

### Scans not running
```bash
# Check Horizon status
php artisan horizon:status

# Check for failed jobs
php artisan horizon:list

# Check Redis connection
php artisan redis:monitor
```

### Email not sending
```bash
# Test mail configuration
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### Database issues
```bash
# Check connection
php artisan db

# Run migrations fresh (caution!)
php artisan migrate:fresh --seed
```

## Monitoring

### Health Check Endpoint
`GET /api/health` returns:
```json
{
  "status": "ok",
  "database": "connected",
  "queue": "running",
  "version": "1.0.0"
}
```

### Log Monitoring
```bash
# Tail application logs
tail -f /home/forge/accessscan.app/storage/logs/laravel.log

# Tail worker logs
tail -f /var/log/accessscan-worker.log
```
