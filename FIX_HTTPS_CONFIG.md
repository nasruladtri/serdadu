# 🔧 Perbaikan: Server Block HTTPS Tidak Aktif

## Masalah yang Ditemukan

Dari output terminal terlihat:
- `client_max_body_size 100M;` ada di konfigurasi
- TAPI `listen 443 ssl default_server;` **di-comment out** (ada `#` di depannya)
- Ini berarti server block HTTPS **tidak aktif**

## ⚡ Solusi

### 1. Cari File Konfigurasi yang Benar

```bash
# Cek semua file konfigurasi
sudo ls -la /etc/nginx/sites-available/
sudo ls -la /etc/nginx/sites-enabled/

# Cek konfigurasi lengkap untuk serdadu.nasruladitri.space
sudo nginx -T | grep -B 20 -A 30 "serdadu.nasruladitri.space"
```

### 2. Edit File Konfigurasi

```bash
# Edit file konfigurasi (ganti dengan nama file yang ditemukan)
sudo nano /etc/nginx/sites-available/serdadu.nasruladitri.space
# atau
sudo nano /etc/nginx/sites-enabled/serdadu.nasruladitri.space
```

### 3. Pastikan Server Block HTTPS Aktif

Cari bagian yang memiliki `# listen 443` (di-comment) dan uncomment atau buat server block HTTPS yang benar:

**Contoh konfigurasi yang benar:**

```nginx
# Server block untuk HTTP - redirect ke HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name serdadu.nasruladitri.space;
    
    # Redirect semua request ke HTTPS
    return 301 https://$server_name$request_uri;
}

# Server block untuk HTTPS - YANG INI HARUS AKTIF
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name serdadu.nasruladitri.space;
    root /var/www/nasruladitri.space/serdadu/public;

    # ⚠️ PENTING: Untuk upload file besar
    client_max_body_size 100M;

    # SSL certificates (sesuaikan path dengan yang ada di server)
    ssl_certificate /etc/letsencrypt/live/serdadu.nasruladitri.space/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/serdadu.nasruladitri.space/privkey.pem;
    
    # SSL configuration (jika menggunakan Let's Encrypt)
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # ⚠️ PENTING: Timeout untuk upload file besar
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. Jika SSL Certificate Belum Ada

Jika SSL certificate belum ada, install dulu dengan Certbot:

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Generate SSL certificate
sudo certbot --nginx -d serdadu.nasruladitri.space

# Certbot akan otomatis:
# - Generate SSL certificate
# - Update konfigurasi Nginx
# - Setup auto-renewal
```

### 5. Test dan Reload Nginx

```bash
# Test konfigurasi
sudo nginx -t

# Jika sukses, reload Nginx
sudo systemctl reload nginx
```

### 6. Verifikasi Server Block HTTPS Aktif

```bash
# Cek apakah server block HTTPS aktif
sudo nginx -T | grep -A 5 "listen 443" | grep -v "^#"

# Seharusnya menampilkan:
# listen 443 ssl http2;
# (tanpa tanda # di depannya)

# Cek client_max_body_size di block HTTPS
sudo nginx -T | grep -B 10 "listen 443" | grep -A 15 "client_max_body_size"
```

## 🔍 Troubleshooting

### Jika `listen 443` Masih Di-comment

1. **Cari baris yang di-comment:**
```bash
sudo grep -n "# listen 443" /etc/nginx/sites-available/*
```

2. **Uncomment baris tersebut:**
```bash
# Edit file dan hapus tanda # di depan listen 443
sudo nano /etc/nginx/sites-available/serdadu.nasruladitri.space
```

3. **Atau gunakan sed untuk uncomment:**
```bash
# Hati-hati: pastikan file yang benar
sudo sed -i 's/# listen 443 ssl/listen 443 ssl/g' /etc/nginx/sites-available/serdadu.nasruladitri.space
```

### Jika SSL Certificate Tidak Ditemukan

```bash
# Cek apakah SSL certificate ada
sudo ls -la /etc/letsencrypt/live/

# Jika belum ada, generate dengan Certbot
sudo certbot --nginx -d serdadu.nasruladitri.space
```

### Cek Port yang Aktif

```bash
# Cek port yang sedang didengarkan oleh Nginx
sudo netstat -tlnp | grep nginx
# atau
sudo ss -tlnp | grep nginx

# Seharusnya menampilkan:
# 0.0.0.0:80 (HTTP)
# 0.0.0.0:443 (HTTPS)
```

## ✅ Checklist

Setelah melakukan perubahan:

- [ ] File konfigurasi Nginx sudah di-edit
- [ ] Server block HTTPS (port 443) **tidak di-comment** (tidak ada `#` di depan `listen 443`)
- [ ] `client_max_body_size 100M;` ada di dalam server block HTTPS
- [ ] SSL certificates sudah terkonfigurasi dengan benar
- [ ] `fastcgi_read_timeout 300;` dan `fastcgi_send_timeout 300;` ada di block PHP
- [ ] Konfigurasi Nginx sudah di-test (`sudo nginx -t`)
- [ ] Nginx sudah di-reload/restart
- [ ] Verifikasi dengan `sudo nginx -T | grep -A 5 "listen 443" | grep -v "^#"`
- [ ] Website bisa diakses via HTTPS
- [ ] Coba upload file lagi

## 🎯 Command Cepat untuk Uncomment

```bash
# 1. Backup file konfigurasi dulu
sudo cp /etc/nginx/sites-available/serdadu.nasruladitri.space /etc/nginx/sites-available/serdadu.nasruladitri.space.backup

# 2. Uncomment listen 443 (hati-hati, pastikan file yang benar)
sudo sed -i 's/# listen 443 ssl/listen 443 ssl/g' /etc/nginx/sites-available/serdadu.nasruladitri.space
sudo sed -i 's/# listen \[::\]:443 ssl/listen [::]:443 ssl/g' /etc/nginx/sites-available/serdadu.nasruladitri.space

# 3. Test konfigurasi
sudo nginx -t

# 4. Jika sukses, reload
sudo systemctl reload nginx

# 5. Verifikasi
sudo nginx -T | grep -A 5 "listen 443" | grep -v "^#"
```

---

**Setelah server block HTTPS aktif dan `client_max_body_size 100M;` ada di dalamnya, error upload seharusnya sudah teratasi!** ✅

