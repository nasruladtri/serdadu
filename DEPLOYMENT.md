# Panduan Deploy SERDADU ke VPS Ubuntu

## Daftar File/Folder yang Harus Ditambahkan ke GitHub

### ✅ File/Folder yang HARUS di-commit ke GitHub:

```
serdadu/
├── app/                          # Semua file aplikasi Laravel
│   ├── Http/
│   ├── Models/
│   ├── Providers/
│   ├── Services/
│   └── View/
├── bootstrap/                    # Bootstrap files
│   ├── app.php
│   ├── cache/                    # (struktur folder, bukan isinya)
│   └── providers.php
├── config/                       # Semua file konfigurasi
├── database/
│   ├── migrations/              # File migrasi database
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories
├── public/                       # Public assets
│   ├── css/
│   ├── img/                      # Gambar statis
│   ├── map/                      # File peta
│   ├── index.php
│   ├── favicon.ico
│   ├── robots.txt
│   └── .htaccess
├── resources/
│   ├── css/
│   ├── js/
│   └── views/                    # Semua file Blade
├── routes/                       # File routing
│   ├── web.php
│   ├── auth.php
│   └── console.php
├── storage/                      # Struktur folder storage
│   ├── app/
│   │   ├── public/               # (folder kosong dengan .gitkeep)
│   │   └── private/              # (folder kosong dengan .gitkeep)
│   ├── framework/
│   │   ├── cache/                # (folder kosong dengan .gitkeep)
│   │   ├── sessions/             # (folder kosong dengan .gitkeep)
│   │   ├── testing/              # (folder kosong dengan .gitkeep)
│   │   └── views/                # (folder kosong dengan .gitkeep)
│   └── logs/                     # (folder kosong dengan .gitkeep)
├── tests/                        # File testing
├── artisan                       # Laravel artisan command
├── composer.json                 # Dependencies PHP
├── composer.lock                 # Lock file composer
├── package.json                  # Dependencies Node.js
├── package-lock.json             # Lock file npm
├── vite.config.js                # Konfigurasi Vite
├── phpunit.xml                   # Konfigurasi PHPUnit
├── .gitignore                    # Git ignore rules
├── README.md                     # Dokumentasi
└── DEPLOYMENT.md                 # File ini
```

### ❌ File/Folder yang TIDAK boleh di-commit:

- `.env` dan `.env.*` (kecuali `.env.example` jika ada)
- `vendor/` (install dengan `composer install`)
- `node_modules/` (install dengan `npm install`)
- `public/build/` (dibuat saat build production)
- `public/hot` (file development)
- `storage/logs/*.log` (log files)
- `storage/framework/cache/*` (cache files)
- `storage/framework/sessions/*` (session files)
- `storage/framework/views/*` (compiled views)
- `bootstrap/cache/*.php` (compiled config)
- `tmp_*.txt`, `temp_*.txt` (file temporary)
- `public/build.zip` (file build)
- `.phpunit.cache/`
- File IDE (`.vscode/`, `.idea/`, dll)

---

## Persiapan Sebelum Push ke GitHub

### 1. Pastikan .gitignore sudah benar
File `.gitignore` sudah diupdate untuk mengecualikan file yang tidak perlu.

### 2. Buat file .env.example (jika belum ada)
Buat file `.env.example` sebagai template untuk konfigurasi:

```bash
cp .env .env.example
# Kemudian edit .env.example, hapus nilai sensitif (password, key, dll)
```

### 3. Pastikan storage structure ada
Pastikan folder storage memiliki struktur yang benar. Jika belum ada `.gitkeep`:

```bash
# Di Windows PowerShell atau Git Bash
touch storage/app/public/.gitkeep
touch storage/app/private/.gitkeep
touch storage/framework/cache/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/testing/.gitkeep
touch storage/framework/views/.gitkeep
touch storage/logs/.gitkeep
```

### 4. Commit dan Push ke GitHub

```bash
git add .
git commit -m "Initial commit: SERDADU project"
git remote add origin https://github.com/USERNAME/serdadu.git
git push -u origin main
```

---

## Instalasi di VPS Ubuntu

### Prerequisites

Pastikan VPS Ubuntu sudah memiliki:
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL/MariaDB
- Node.js 18+ dan NPM
- Nginx atau Apache
- Git

### Langkah 1: Update Sistem

```bash
sudo apt update
sudo apt upgrade -y
```

### Langkah 2: Install PHP 8.2 dan Extensions

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-intl
```

### Langkah 3: Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Langkah 4: Install Node.js dan NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Langkah 5: Install MySQL/MariaDB

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### Langkah 6: Install Nginx

```bash
sudo apt install -y nginx
```

### Langkah 7: Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/USERNAME/serdadu.git
sudo chown -R $USER:$USER /var/www/serdadu
cd serdadu
```

### Langkah 8: Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install

# Build assets untuk production
npm run build
```

### Langkah 9: Konfigurasi Environment

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit file .env sesuai kebutuhan
nano .env
```

**Konfigurasi penting di `.env`:**

```env
APP_NAME="SERDADU"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=serdadu_db
DB_USERNAME=serdadu_user
DB_PASSWORD=your_secure_password
```

### Langkah 10: Setup Database

```bash
# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE serdadu_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'serdadu_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON serdadu_db.* TO 'serdadu_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force
```

### Langkah 11: Setup Storage dan Permissions

```bash
# Buat symbolic link untuk storage
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data /var/www/serdadu
sudo chmod -R 755 /var/www/serdadu
sudo chmod -R 775 /var/www/serdadu/storage
sudo chmod -R 775 /var/www/serdadu/bootstrap/cache
```

### Langkah 12: Konfigurasi Nginx

Buat file konfigurasi Nginx:

```bash
sudo nano /etc/nginx/sites-available/serdadu
```

Isi dengan konfigurasi berikut:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/serdadu/public;

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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi:

```bash
sudo ln -s /etc/nginx/sites-available/serdadu /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Langkah 13: Setup SSL dengan Let's Encrypt (Opsional tapi Disarankan)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Langkah 14: Optimasi Laravel untuk Production

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Langkah 15: Setup Supervisor untuk Queue (Jika menggunakan Queue)

```bash
sudo apt install supervisor -y
sudo nano /etc/supervisor/conf.d/serdadu-worker.conf
```

Isi dengan:

```ini
[program:serdadu-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/serdadu/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/serdadu/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start serdadu-worker:*
```

### Langkah 16: Setup Cron Job untuk Scheduler

```bash
sudo crontab -e -u www-data
```

Tambahkan baris berikut:

```
* * * * * cd /var/www/serdadu && php artisan schedule:run >> /dev/null 2>&1
```

---

## Update Deployment (Setelah Perubahan)

Setelah melakukan perubahan dan push ke GitHub, di VPS:

```bash
cd /var/www/serdadu

# Pull perubahan terbaru
git pull origin main

# Install dependencies baru (jika ada)
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Clear dan rebuild cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (jika ada)
php artisan migrate --force

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
sudo supervisorctl restart serdadu-worker:*
```

---

## Troubleshooting

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/serdadu
sudo chmod -R 755 /var/www/serdadu
sudo chmod -R 775 /var/www/serdadu/storage
sudo chmod -R 775 /var/www/serdadu/bootstrap/cache
```

### Check Logs
```bash
# Laravel logs
tail -f /var/www/serdadu/storage/logs/laravel.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### Test PHP-FPM
```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

### Test Nginx Configuration
```bash
sudo nginx -t
sudo systemctl status nginx
sudo systemctl restart nginx
```

---

## Checklist Deploy

- [ ] Semua file sudah di-commit dan push ke GitHub
- [ ] VPS sudah memiliki PHP 8.2+, Composer, MySQL, Node.js, Nginx
- [ ] Repository sudah di-clone di VPS
- [ ] Dependencies sudah di-install (composer & npm)
- [ ] File `.env` sudah dikonfigurasi
- [ ] Database sudah dibuat dan migrations sudah dijalankan
- [ ] Storage permissions sudah benar
- [ ] Nginx sudah dikonfigurasi dan diaktifkan
- [ ] SSL sudah di-setup (jika menggunakan HTTPS)
- [ ] Laravel cache sudah dioptimasi
- [ ] Queue worker sudah di-setup (jika diperlukan)
- [ ] Cron job sudah di-setup
- [ ] Website sudah bisa diakses

---

## Catatan Penting

1. **Jangan commit file `.env`** ke GitHub karena berisi informasi sensitif
2. **Selalu gunakan `--no-dev`** saat install composer di production
3. **Pastikan `APP_DEBUG=false`** di production
4. **Gunakan HTTPS** untuk keamanan
5. **Backup database** secara rutin
6. **Monitor logs** untuk error dan security issues
7. **Update dependencies** secara berkala untuk security patches

---

## Support

Jika ada masalah saat deployment, periksa:
- Laravel logs: `storage/logs/laravel.log`
- Nginx logs: `/var/log/nginx/error.log`
- PHP-FPM logs: `/var/log/php8.2-fpm.log`
- System logs: `journalctl -u nginx` atau `journalctl -u php8.2-fpm`

