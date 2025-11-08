# 🚨 Solusi: Error Upload File - "client intended to send too large body"

## Masalah dari Log Error

Dari log Nginx terlihat:
```
client intended to send too large body: 2914820 bytes
```

Ini berarti file ~2.9 MB ditolak karena `client_max_body_size` di Nginx masih default (1M).

## ⚡ Solusi Langsung

### 1. Temukan File Konfigurasi Nginx yang Benar

```bash
# Cek file konfigurasi yang aktif untuk domain serdadu.nasruladitri.space


sudo ls -la /etc/nginx/sites-enabled/

# Cek konfigurasi yang sedang digunakan
sudo nginx -T | grep -A 10 "server_name.*serdadu"
```

### 2. Edit File Konfigurasi Nginx

Kemungkinan file konfigurasinya adalah salah satu dari:
- `/etc/nginx/sites-available/nasruladitri.space`
- `/etc/nginx/sites-available/serdadu`
- `/etc/nginx/sites-available/default`

```bash
# Edit file konfigurasi (ganti dengan nama file yang ditemukan di step 1)
sudo nano /etc/nginx/sites-available/nasruladitri.space
```

### 3. Pastikan Ada `client_max_body_size` di Block Server

**⚠️ PENTING:** Jika website menggunakan HTTPS, pastikan **server block untuk port 443 (HTTPS)** memiliki `client_max_body_size 100M;`, bukan hanya block port 80!

Cari block `server` yang berisi `serdadu.nasruladitri.space`:

```bash
# Cek semua server block
sudo nginx -T | grep -B 10 -A 20 "server_name.*serdadu"
```

Pastikan di block **HTTPS (port 443)** ada:

```nginx
server {
    listen 443 ssl http2;  # ← Block HTTPS ini yang digunakan
    server_name serdadu.nasruladitri.space;
    root /var/www/nasruladitri.space/serdadu/public;
    
    # ⚠️ PENTING: Tambahkan baris ini di block HTTPS
    client_max_body_size 100M;
    
    # SSL configuration...
    # ... konfigurasi lainnya
}
```

**Jika hanya ada di block HTTP (port 80) tapi website diakses via HTTPS, setting tidak akan bekerja!**

### 4. Pastikan Ada Timeout di Block PHP

Di dalam block `location ~ \.php$`, pastikan ada:

```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    
    # ⚠️ PENTING: Tambahkan timeout
    fastcgi_read_timeout 300;
    fastcgi_send_timeout 300;
}
```

### 5. Test dan Restart Nginx

```bash
# Test konfigurasi
sudo nginx -t

# Jika sukses, reload Nginx

# atau
sudo systemctl restart nginx
```

### 6. Verifikasi Konfigurasi

```bash
# Cek apakah client_max_body_size sudah terpasang
sudo nginx -T | grep client_max_body_size

# Jika menggunakan HTTP dan HTTPS, seharusnya menampilkan 2 baris:
# client_max_body_size 100M;  (di block HTTP)
# client_max_body_size 100M;  (di block HTTPS) ← YANG INI YANG PENTING

# Cek secara detail di block HTTPS
sudo nginx -T | grep -B 5 -A 5 "listen 443" | grep -A 10 "client_max_body_size"
```

## 🔍 Troubleshooting Jika Masih Error

### Cek File Konfigurasi yang Aktif

```bash
# Cek semua server block
sudo nginx -T | grep -B 5 -A 15 "serdadu.nasruladitri.space"
```

### Cek Apakah Ada Multiple Server Block

Kemungkinan ada beberapa server block, pastikan yang benar di-edit:

```bash
# List semua konfigurasi
sudo nginx -T | grep "server_name"
```

### Cek Konfigurasi PHP Juga

Selain Nginx, pastikan PHP juga sudah dikonfigurasi:

```bash
# Untuk PHP 8.4
sudo nano /etc/php/8.4/fpm/php.ini

# Pastikan nilai berikut:
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

### Cek Log Error Lagi

Setelah perubahan, cek log error:

```bash
sudo tail -f /var/log/nginx/error.log
```

Upload file lagi dan lihat apakah masih ada error.

## 📝 Contoh Konfigurasi Lengkap

### File: `/etc/nginx/sites-available/nasruladitri.space`

**⚠️ PENTING:** Jika menggunakan HTTPS, pastikan kedua server block (HTTP dan HTTPS) memiliki `client_max_body_size 100M;`

#### Server Block untuk HTTP (Port 80) - Redirect ke HTTPS
```nginx
# Server block untuk HTTP - redirect ke HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name serdadu.nasruladitri.space;
    
    # Redirect ke HTTPS
    return 301 https://$host$request_uri;
}
```

#### Server Block untuk HTTPS (Port 443) - Yang Aktif
```nginx
# Server block untuk HTTPS - YANG INI YANG DIGUNAKAN
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name serdadu.nasruladitri.space;
    root /var/www/nasruladitri.space/serdadu/public;

    # ⚠️ PENTING: Untuk upload file besar (HARUS ADA DI BLOCK HTTPS INI)
    client_max_body_size 100M;

    # SSL certificates
    ssl_certificate /etc/letsencrypt/live/serdadu.nasruladitri.space/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/serdadu.nasruladitri.space/privkey.pem;
    
    # SSL configuration
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

## ⚠️ PENTING: Jika Menggunakan HTTPS

Jika website menggunakan HTTPS (SSL), pastikan **server block untuk port 443** juga memiliki `client_max_body_size 100M;`:

```bash
# Cek server block untuk HTTPS
sudo nginx -T | grep -B 10 -A 20 "listen 443"
```

Contoh konfigurasi untuk HTTPS:

```nginx
server {
    listen 443 ssl http2;
    server_name serdadu.nasruladitri.space;
    root /var/www/nasruladitri.space/serdadu/public;

    # ⚠️ PENTING: Untuk upload file besar (PASTIKAN ADA DI BLOCK HTTPS INI JUGA)
    client_max_body_size 100M;

    # SSL configuration
    ssl_certificate /etc/letsencrypt/live/serdadu.nasruladitri.space/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/serdadu.nasruladitri.space/privkey.pem;
    
    # ... konfigurasi lainnya
}
```

**Sangat penting:** Jika konfigurasi hanya di block HTTP (port 80) tapi website diakses via HTTPS, setting tidak akan berlaku!

## ✅ Checklist

Setelah melakukan perubahan:

- [ ] File konfigurasi Nginx sudah di-edit
- [ ] `client_max_body_size 100M;` sudah ditambahkan di **server block HTTP (port 80)**
- [ ] `client_max_body_size 100M;` sudah ditambahkan di **server block HTTPS (port 443)** ← **PENTING!**
- [ ] `fastcgi_read_timeout 300;` dan `fastcgi_send_timeout 300;` sudah ditambahkan di kedua block
- [ ] Konfigurasi Nginx sudah di-test (`sudo nginx -t`)
- [ ] Nginx sudah di-reload/restart
- [ ] PHP configuration sudah di-update
- [ ] PHP-FPM sudah di-restart
- [ ] Verifikasi dengan `sudo nginx -T | grep client_max_body_size` (harus muncul 2 kali jika ada HTTP dan HTTPS)
- [ ] Coba upload file lagi

## 🎯 Command Cepat (All-in-One)

```bash
# 1. Update PHP config
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.4/fpm/php.ini

# 2. Cari file konfigurasi Nginx
CONFIG_FILE=$(sudo nginx -T 2>/dev/null | grep -B 10 "serdadu.nasruladitri.space" | grep "server_name" | head -1 | awk '{print $NF}' | sed 's/;//')
echo "Konfigurasi file: $CONFIG_FILE"

# 3. Edit file konfigurasi (manual - buka dengan nano)
sudo nano /etc/nginx/sites-available/nasruladitri.space
# Tambahkan: client_max_body_size 100M; di dalam server block

# 4. Test dan restart
sudo nginx -t && sudo systemctl reload nginx
sudo systemctl restart php8.4-fpm

# 5. Verifikasi
sudo nginx -T | grep client_max_body_size
php -i | grep -E "upload_max_filesize|post_max_size"
```

---

**Setelah melakukan semua langkah di atas, coba upload file Excel lagi. Error seharusnya sudah teratasi!** ✅

