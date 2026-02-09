# AccessScan — ADA/WCAG Compliance Checker

<p align="center">
  <img src="https://accessscan.app/icon.svg" width="120" alt="AccessScan Logo">
</p>

<p align="center">
  <strong>Automated accessibility testing for small businesses</strong>
</p>

<p align="center">
  <a href="https://accessscan.app">Website</a> •
  <a href="https://accessscan.app/pricing">Pricing</a> •
  <a href="https://accessscan.app/api/docs">API Docs</a>
</p>

---

## Overview

AccessScan is a SaaS application that helps small businesses ensure their websites meet ADA/WCAG accessibility standards. Built with Laravel 12, Livewire 4, and Tailwind CSS 4.

### Features

- 🚀 **Instant Scanning** — Automated WCAG A/AA compliance checks using Pa11y
- 📊 **Detailed Reports** — Executive summaries, issue breakdowns, and fix recommendations
- 💳 **Freemium Model** — Free tier (5 scans/mo) + Pro ($29/mo) + Lifetime ($197)
- 📈 **Scheduled Scans** — Daily, weekly, or monthly automated monitoring
- 🔗 **API Access** — REST API for developers and integrations
- 📤 **Export Options** — PDF, CSV, and JSON report downloads (Pro)

### Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Livewire 4, Tailwind CSS 4
- **Database:** MySQL 8.0+ or PostgreSQL
- **Queue:** Laravel Horizon + Redis
- **Billing:** Laravel Cashier (Stripe)
- **Authentication:** Laravel Breeze + Sanctum (API)

---

## Quick Start

### Requirements

- PHP 8.2+
- Composer 2+
- Node.js 20+
- MySQL 8.0+ or PostgreSQL
- Redis 7+
- Pa11y CLI: `npm install -g pa11y@6`

### Installation

```bash
# Clone the repository
git clone git@github.com:jasonfrye/access-scan.git
cd access-scan

# Install dependencies
composer install
npm ci

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure your database and Stripe keys in .env

# Run migrations
php artisan migrate --seed

# Build assets
npm run build

# Start the development server
php artisan serve
```

Visit `http://localhost:8000` to see the application.

---

## Deployment

### Laravel Forge (Recommended)

See [DEPLOY.md](DEPLOY.md) for complete deployment instructions including:

- One-click Forge setup
- Environment variable configuration
- Queue worker setup
- Scheduled tasks
- Post-deploy checklist

### Quick Deploy

```bash
# Make deploy script executable
chmod +x deploy.sh

# Run deployment (defaults to production)
./deploy.sh

# Or specify environment and branch
./deploy.sh production develop
```

---

## API Documentation

Full API documentation is available at: **https://accessscan.app/api/docs**

### Quick API Example

```bash
# Create a scan
curl -X POST https://accessscan.app/api/v1/scans \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://example.com"}'

# Check status
curl https://accessscan.app/api/v1/scans/1/status \
  -H "Authorization: Bearer YOUR_API_KEY"

# Get results
curl https://accessscan.app/api/v1/scans/1 \
  -H "Authorization: Bearer YOUR_API_KEY"
```

---

## Pricing Plans

| Feature | Free | Pro ($29/mo) | Lifetime ($197) |
|---------|------|--------------|-----------------|
| Scans/month | 5 | 50 | Unlimited |
| Pages/scan | 5 | 100 | Unlimited |
| Scheduled scans | ❌ | ✅ | ✅ |
| PDF/CSV export | ❌ | ✅ | ✅ |
| API access | ❌ | ❌ | ✅ |
| White-label | ❌ | ❌ | ✅ |
| Priority support | ❌ | ❌ | ✅ |

---

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=ScanApiControllerTest
```

**Test Status:** 208 passing tests

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This project is licensed under the MIT License.

---

## Support

- 📧 Email: support@accessscan.app
- 🐛 Issues: GitHub Issues
- 📖 Docs: https://accessscan.app/docs
