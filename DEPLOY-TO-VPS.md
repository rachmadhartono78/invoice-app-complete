# 🚀 Deploy ke VPS dengan Docker

## Step-by-Step untuk VPS dengan Nginx

### 1. **Di VPS - Install Docker & Dependencies**

```bash
ssh root@your-vps-ip

# Update system
apt update && apt upgrade -y

# Install Docker
apt install -y docker.io docker-compose curl git

# Start & enable Docker
systemctl start docker
systemctl enable docker

# Add user ke docker group (optional)
usermod -aG docker $USER
```

---

### 2. **Clone Project ke VPS**

```bash
cd /var/www
git clone https://github.com/your-username/repo.git invoice-app
cd invoice-app
```

---

### 3. **Setup Environment Variables**

```bash
# Copy template production
cp .env.production.example .env.production

# Edit dengan credentials aman
nano .env.production
```

**Perlu di-update:**
- `APP_KEY`: Generate dengan `php artisan key:generate` (atau copy dari local)
- `DB_NAME`, `DB_USER`, `DB_PASSWORD`: Ganti password yang aman!
- `APP_URL`: Domain Anda (misal: https://invoice.example.com)
- Email settings (jika perlu)

**Contoh aman menggunakan env vars:**
```bash
# Export sebagai environment variable
export DB_ROOT_PASSWORD="SuperSecure@Password123"
export DB_PASSWORD="SecurePass456"
```

---

### 4. **Build & Run Containers**

```bash
# Build images (sekali saja)
docker-compose -f docker-compose.prod.yml build

# Start services
docker-compose -f docker-compose.prod.yml up -d

# Check status
docker-compose -f docker-compose.prod.yml ps
```

---

### 5. **Setup Database**

```bash
# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Seed data (optional)
docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --class=ProductionInvoiceSeeder

# Verify
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> Invoice::count()
2  # Atau jumlah invoice Anda
>>> exit
```

---

### 6. **Configure Nginx Reverse Proxy** (Di server host, bukan container)

```bash
# Buat config
nano /etc/nginx/sites-available/invoice-app
```

**Content:**
```nginx
upstream invoice_app {
    server localhost:80;
}

server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    
    # Redirect ke HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    location / {
        proxy_pass http://invoice_app;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Enable & test
ln -s /etc/nginx/sites-available/invoice-app /etc/nginx/sites-enabled/
nginx -t

# Restart Nginx
systemctl restart nginx
```

---

### 7. **Setup SSL Certificate (Let's Encrypt)**

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Get certificate
certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Auto-renew
certbot renew --dry-run
```

---

### 8. **Monitor & Maintain**

```bash
# View logs
docker-compose -f docker-compose.prod.yml logs -f app

# Restart services
docker-compose -f docker-compose.prod.yml restart

# Stop services
docker-compose -f docker-compose.prod.yml down

# Backup database
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_NAME} > backup-$(date +%Y%m%d).sql
```

---

### 9. **Update Code (Pull Changes)**

```bash
git pull origin main

# Rebuild images jika ada Dockerfile changes
docker-compose -f docker-compose.prod.yml build

# Restart
docker-compose -f docker-compose.prod.yml up -d
```

---

## ✅ Quick Checklist

- [ ] Docker & Docker-Compose installed
- [ ] Project cloned ke `/var/www/invoice-app`
- [ ] `.env.production` configured dengan credentials aman
- [ ] `docker-compose.prod.yml build` berhasil
- [ ] Containers running (`docker-compose ps` shows all green)
- [ ] Database migrations done
- [ ] Nginx configured & SSL installed
- [ ] Domain pointing to VPS IP
- [ ] Can access https://your-domain.com

---

## 🆘 Troubleshooting

**Error: Container exits immediately**
```bash
docker-compose -f docker-compose.prod.yml logs app
# Cek error message
```

**Error: Port 80/443 already in use**
```bash
# Kill service using port
lsof -i :80
sudo kill -9 <PID>
```

**Error: Database connection failed**
```bash
# Check if db container is running
docker-compose -f docker-compose.prod.yml logs db

# Verify environment variables
docker-compose -f docker-compose.prod.yml exec app env | grep DB_
```

---

**Kalau ada issue, share output error di console atau logs file!** 🚀
