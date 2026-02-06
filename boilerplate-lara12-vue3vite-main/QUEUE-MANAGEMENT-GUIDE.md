# Laravel Queue Management Guide

## Overview

When using `ShouldBroadcast` + `Queueable`, notifications are queued and need a **queue worker** to process them. This guide covers how to run queue workers in development and production.

---

## Why Use Queues?

✅ **Better Performance** - Notifications don't block your application
✅ **Reliability** - Failed jobs can be retried automatically
✅ **Scalability** - Process multiple notifications concurrently
✅ **Production Standard** - Professional Laravel applications use queues

---

## Development Setup

### Option 1: Manual Queue Worker (Current)

Run this in a separate terminal:

```bash
php artisan queue:listen
```

**Pros:** Simple, shows live output
**Cons:** Must run manually each time

### Option 2: Add to Composer Dev Script

Update `composer.json`:

```json
"scripts": {
    "dev": [
        "Composer\\Config::disableProcessTimeout",
        "@php artisan serve & echo $! > server.pid",
        "@php artisan queue:listen & echo $! > queue.pid",
        "@php artisan reverb:start & echo $! > reverb.pid",
        "wait"
    ],
    "stop": [
        "kill $(cat server.pid) || true",
        "kill $(cat queue.pid) || true",
        "kill $(cat reverb.pid) || true",
        "rm -f server.pid queue.pid reverb.pid"
    ]
}
```

Then run:
```bash
composer run dev
```

### Option 3: Use Docker (Simplest)

Already configured! Just run:

```bash
docker-compose up -d
```

This starts:
- Laravel app (ecos-app)
- Queue worker (ecos-queue) ✓
- Reverb server (ecos-reverb) ✓
- MySQL database (ecos-db)
- Nginx (ecos-nginx)

Check queue worker logs:
```bash
docker logs -f ecos-queue
```

---

## Production Setup

### Option 1: Docker (Recommended)

Already configured in `docker-compose.yml`!

The `queue` service:
- Auto-starts with `docker-compose up -d`
- Auto-restarts if it crashes
- Processes jobs continuously

**Deploy:**
```bash
docker-compose up -d
```

**Monitor:**
```bash
# View logs
docker logs -f ecos-queue

# Restart after code changes
docker-compose restart queue

# Check status
docker-compose ps queue
```

---

### Option 2: Supervisor (Traditional VPS)

Already configured in `supervisor-queue-worker.conf`!

**Install Supervisor:**
```bash
# Ubuntu/Debian
sudo apt install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

**Setup:**
```bash
# Copy config
sudo cp supervisor-queue-worker.conf /etc/supervisor/conf.d/laravel-queue-worker.conf

# Edit paths (replace /path/to/your/project)
sudo nano /etc/supervisor/conf.d/laravel-queue-worker.conf

# Apply changes
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue-worker
```

**Manage:**
```bash
# Check status
sudo supervisorctl status laravel-queue-worker

# View logs
sudo tail -f /var/www/your-project/storage/logs/queue-worker.log

# Restart (after code changes)
sudo supervisorctl restart laravel-queue-worker

# Stop
sudo supervisorctl stop laravel-queue-worker
```

---

### Option 3: Systemd Service

Create `/etc/systemd/system/laravel-queue.service`:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5s
ExecStart=/usr/bin/php /var/www/your-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=append:/var/www/your-project/storage/logs/queue-worker.log
StandardError=append:/var/www/your-project/storage/logs/queue-worker-error.log

[Install]
WantedBy=multi-user.target
```

**Manage:**
```bash
# Enable and start
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue

# Check status
sudo systemctl status laravel-queue

# View logs
sudo journalctl -u laravel-queue -f

# Restart (after code changes)
sudo systemctl restart laravel-queue
```

---

## Queue Configuration

### Current Setup (.env)

```bash
QUEUE_CONNECTION=database
```

**How it works:**
- Jobs stored in `jobs` table in MySQL
- Queue worker polls the table for new jobs
- Perfect for small to medium apps

### For High Traffic (Recommended for Production)

Use **Redis** for better performance:

```bash
# .env
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Advantages:**
- Much faster than database
- Better for high-traffic apps
- Industry standard

**Setup Redis:**
```bash
# Ubuntu
sudo apt install redis-server

# Start
sudo systemctl start redis-server

# Enable on boot
sudo systemctl enable redis-server
```

Update `docker-compose.yml` to add Redis:
```yaml
redis:
  image: redis:alpine
  container_name: ecos-redis
  restart: unless-stopped
  ports:
    - "6379:6379"
  networks:
    - ecos
```

---

## Queue Commands

### Monitor Queue

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush

# Monitor queue in real-time
php artisan queue:monitor
```

### Worker Commands

```bash
# Start worker (stays running)
php artisan queue:work

# Process one job and exit
php artisan queue:work --once

# Process jobs for 60 seconds then exit
php artisan queue:work --max-time=60

# Process specific queue
php artisan queue:work --queue=high,default,low

# Process jobs with timeout
php artisan queue:work --timeout=60
```

---

## Troubleshooting

### Notifications Not Being Received

1. **Check if queue worker is running:**
   ```bash
   # Docker
   docker ps | grep queue

   # Supervisor
   sudo supervisorctl status laravel-queue-worker

   # Systemd
   sudo systemctl status laravel-queue

   # Manual
   ps aux | grep "queue:work"
   ```

2. **Check jobs table:**
   ```sql
   SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
   ```

   If jobs are stuck in the table, the queue worker isn't running!

3. **Check failed jobs:**
   ```bash
   php artisan queue:failed
   ```

   If jobs are failing, check logs for errors.

4. **Check Reverb is running:**
   ```bash
   ps aux | grep reverb
   netstat -tulpn | grep 8080
   ```

### Worker Not Processing Jobs

1. **Restart queue worker** (always do this after code changes):
   ```bash
   # Docker
   docker-compose restart queue

   # Supervisor
   sudo supervisorctl restart laravel-queue-worker

   # Systemd
   sudo systemctl restart laravel-queue
   ```

2. **Check queue connection:**
   ```bash
   php artisan queue:work --once
   ```
   Should process one job. If error appears, fix it.

3. **Check worker timeout:**
   Increase `--max-time` if jobs take long to process.

---

## Production Checklist

- [ ] Choose queue backend (database or Redis)
- [ ] Configure queue connection in `.env`
- [ ] Setup Supervisor/Systemd/Docker to run queue worker
- [ ] Configure worker to auto-restart on failure
- [ ] Setup monitoring/logging
- [ ] Test notification broadcasting end-to-end
- [ ] Configure failed job handling
- [ ] Setup alerts for failed jobs (optional)

---

## Deployment Workflow

### After Code Changes

**Docker:**
```bash
docker-compose restart queue reverb
```

**Supervisor:**
```bash
sudo supervisorctl restart laravel-queue-worker
sudo supervisorctl restart laravel-reverb
```

**Systemd:**
```bash
sudo systemctl restart laravel-queue
sudo systemctl restart laravel-reverb
```

### Why Restart?

Queue workers load your code into memory. After code changes, you must restart them to load the new code. Otherwise, they'll still run the old code!

---

## Summary

### Development (Current Setup)

**Option A: Manual**
```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan queue:listen

# Terminal 3
php artisan reverb:start
```

**Option B: Docker (Recommended)**
```bash
docker-compose up -d
```

### Production (Recommended)

**Use Docker:**
```bash
docker-compose up -d
```

This handles everything:
- ✅ Queue worker runs automatically
- ✅ Reverb server runs automatically
- ✅ Auto-restarts on failure
- ✅ Easy to monitor and manage

---

## Quick Reference

| Task | Command |
|------|---------|
| Start queue worker | `php artisan queue:work` |
| Monitor queue | `php artisan queue:monitor` |
| Check failed jobs | `php artisan queue:failed` |
| Retry failed jobs | `php artisan queue:retry all` |
| Restart Docker queue | `docker-compose restart queue` |
| View Docker logs | `docker logs -f ecos-queue` |
| Restart Supervisor | `sudo supervisorctl restart laravel-queue-worker` |

---

Your queue system is now production-ready! 🚀
