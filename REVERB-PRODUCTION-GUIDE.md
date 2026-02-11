# Laravel Reverb Production Deployment Guide

## Overview

This guide covers how to deploy Laravel Reverb for real-time WebSocket notifications in production.

---

## Option 1: Docker (Recommended for Modern Deployments)

### Setup

1. **Update docker-compose.yml** (Already done!)
   - Reverb service runs on port 8081 externally (8080 internally)
   - Auto-restarts on failure

2. **Start services:**
   ```bash
   docker-compose up -d
   ```

3. **View Reverb logs:**
   ```bash
   docker logs -f ecos-reverb
   ```

### Nginx Proxy Configuration

Add to your nginx config to proxy WebSocket connections:

```nginx
# /etc/nginx/sites-available/yourdomain.com

upstream reverb {
    server 127.0.0.1:8081;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    # SSL configuration
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    # WebSocket endpoint for Reverb
    location /app {
        proxy_pass http://reverb;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_read_timeout 86400;
    }

    # Your Laravel application
    location / {
        proxy_pass http://127.0.0.1:8000;
        # ... other Laravel proxy settings
    }
}
```

---

## Option 2: Supervisor (Traditional VPS/Server)

### Install Supervisor

```bash
# Ubuntu/Debian
sudo apt install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

### Configure Reverb Worker

Copy the configuration:

```bash
sudo cp supervisor-reverb.conf /etc/supervisor/conf.d/laravel-reverb.conf
```

Edit the file:

```bash
sudo nano /etc/supervisor/conf.d/laravel-reverb.conf
```

Update paths:
- Replace `/var/www/your-project` with your actual project path
- Update `user=www-data` to your web server user

### Start Reverb

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-reverb
```

### Monitor Reverb

```bash
# Check status
sudo supervisorctl status laravel-reverb

# View logs
sudo tail -f /var/www/your-project/storage/logs/reverb.log

# Restart if needed
sudo supervisorctl restart laravel-reverb
```

---

## Option 3: Systemd Service (Modern Linux)

### Install Service

```bash
# Copy service file
sudo cp laravel-reverb.service /etc/systemd/system/

# Edit paths
sudo nano /etc/systemd/system/laravel-reverb.service

# Reload systemd
sudo systemctl daemon-reload

# Enable auto-start on boot
sudo systemctl enable laravel-reverb

# Start service
sudo systemctl start laravel-reverb
```

### Manage Service

```bash
# Check status
sudo systemctl status laravel-reverb

# View logs
sudo journalctl -u laravel-reverb -f

# Restart
sudo systemctl restart laravel-reverb

# Stop
sudo systemctl stop laravel-reverb
```

---

## Production Environment Configuration

### 1. Update .env for Production

```bash
# Broadcasting
BROADCAST_DRIVER=pusher

# Reverb - Use WSS (secure WebSocket) in production
REVERB_SCHEME=https
REVERB_HOST=yourdomain.com
REVERB_PORT=443

# Generate new secure credentials
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-secure-key
REVERB_APP_SECRET=your-secure-secret
```

### 2. Generate Secure Reverb Credentials

```bash
# Generate random secure keys
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"
```

### 3. Update Frontend .env Variables

Make sure these are in your `.env`:

```bash
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Then rebuild frontend:

```bash
npm run build
```

---

## SSL/TLS Configuration (Required for Production)

Reverb needs SSL in production. You have 2 options:

### Option A: Use Nginx as SSL Proxy (Recommended)

Nginx handles SSL, proxies to Reverb (shown in nginx config above)

**Advantages:**
- Easier SSL management
- Better performance
- Can use Let's Encrypt easily

### Option B: Configure Reverb with SSL Directly

Update `config/reverb.php`:

```php
'servers' => [
    'reverb' => [
        // ...
        'options' => [
            'tls' => [
                'local_cert' => '/path/to/ssl/cert.pem',
                'local_pk' => '/path/to/ssl/key.pem',
                'verify_peer' => false,
            ],
        ],
    ],
],
```

---

## Monitoring & Troubleshooting

### Check if Reverb is Running

```bash
# Check process
ps aux | grep reverb

# Check port
netstat -tulpn | grep 8080

# Test WebSocket connection
curl -i -N -H "Connection: Upgrade" \
     -H "Upgrade: websocket" \
     -H "Sec-WebSocket-Version: 13" \
     -H "Sec-WebSocket-Key: test" \
     http://localhost:8080/app/your-app-key
```

### Common Issues

1. **Connection Refused**
   - Check if Reverb is running
   - Check firewall: `sudo ufw allow 8080`

2. **403 Forbidden on Channel Auth**
   - Verify `/api/broadcasting/auth` route exists
   - Check Bearer token is being sent
   - Check user is authenticated

3. **Messages Not Broadcasting**
   - Check `BROADCAST_DRIVER=pusher` in `.env`
   - Check Reverb logs for errors
   - Verify notification implements `ShouldBroadcast`

### View Reverb Stats

Access Reverb's built-in stats:

```bash
curl http://localhost:8080/apps/your-app-id/channels
```

---

## Performance Tuning

### For High Traffic

1. **Use Redis for scaling:**

```bash
# .env
REVERB_SCALING_ENABLED=true
REVERB_SCALING_CHANNEL=reverb
```

2. **Run multiple Reverb instances** behind a load balancer

3. **Increase connection limits** in `config/reverb.php`

---

## Deployment Checklist

- [ ] Set `BROADCAST_DRIVER=pusher` in production `.env`
- [ ] Configure secure `REVERB_APP_KEY` and `REVERB_APP_SECRET`
- [ ] Set `REVERB_SCHEME=https` and `REVERB_PORT=443`
- [ ] Setup Supervisor/Systemd/Docker to keep Reverb running
- [ ] Configure Nginx to proxy WebSocket connections with SSL
- [ ] Run `npm run build` after updating Vite env variables
- [ ] Test WebSocket connection from frontend
- [ ] Setup monitoring/logging for Reverb process
- [ ] Configure firewall to allow WebSocket port
- [ ] Test notification broadcasting end-to-end

---

## Quick Start Commands

### Development (Current Setup)
```bash
# Terminal 1: Start Reverb
php artisan reverb:start

# Terminal 2: Start Laravel
php artisan serve
```

### Production with Docker
```bash
docker-compose up -d
docker logs -f ecos-reverb
```

### Production with Supervisor
```bash
sudo supervisorctl start laravel-reverb
sudo supervisorctl status laravel-reverb
```

---

## Summary

**For your production deployment, I recommend:**

1. **Use Docker** (already configured in `docker-compose.yml`)
2. **Use Nginx as SSL proxy** for WebSocket connections
3. **Remove Queueable** from notifications (simpler, no queue worker needed)
4. **Monitor Reverb logs** regularly

This gives you a production-ready real-time notification system! 🚀
