# AccessScan Deployment Configuration
# Copy these files to appropriate locations after deployment

## Systemd Service (Queue Workers)
# Copy to: /etc/systemd/system/accessscan-worker.service
# Then run:
#   sudo systemctl daemon-reload
#   sudo systemctl enable accessscan-worker
#   sudo systemctl start accessscan-worker

## Cron (Scheduled Tasks)
# Add to crontab: crontab -u www-data -e
# Or copy to: /etc/cron.d/accessscan
