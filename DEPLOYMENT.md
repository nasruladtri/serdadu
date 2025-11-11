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
- **PHP 8.2 atau lebih tinggi** (disarankan PHP 8.4)
  - Project ini membutuhkan minimum PHP 8.2.0 (lihat `composer.json`)
  - PHP 8.4 adalah versi terbaru dan sangat direkomendasikan
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

### Langkah 2: Install PHP dan Extensions

**⚠️ Catatan Penting:**
- Project ini membutuhkan **minimum PHP 8.2.0** (lihat `composer.json`)
- **PHP 8.4** sangat disarankan karena versi terbaru dengan fitur dan performa terbaik
- Jangan gunakan PHP versi di bawah 8.2 (tidak kompatibel dengan Laravel 11)

**Instalasi PHP 8.4 (Disarankan):**

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.4 (atau versi yang diinginkan)
sudo apt install -y php8.4 \
    php8.4-fpm \
    php8.4-cli \
    php8.4-common \
    php8.4-mysql \
    php8.4-zip \
    php8.4-gd \
    php8.4-mbstring \
    php8.4-curl \
    php8.4-xml \
    php8.4-bcmath \
    php8.4-intl

# Verifikasi extension yang terinstall
php -m | grep -i gd
php -m | grep -i mbstring
php -m | grep -i xml
```

**⚙️ Konfigurasi PHP untuk Upload File Besar (PENTING untuk Import Excel):**

```bash
# Edit PHP.ini untuk FPM (web server)
sudo nano /etc/php/8.4/fpm/php.ini

# Cari dan ubah nilai berikut:
# upload_max_filesize = 100M
# post_max_size = 100M
# max_execution_time = 300
# max_input_time = 300
# memory_limit = 256M

# Edit PHP.ini untuk CLI (jika diperlukan)
sudo nano /etc/php/8.4/cli/php.ini
# Lakukan perubahan yang sama

# Restart PHP-FPM setelah perubahan
sudo systemctl restart php8.4-fpm
```

**Atau gunakan command cepat untuk mengubah konfigurasi:**

```bash
# Untuk PHP 8.4 FPM
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_input_time = .*/max_input_time = 300/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.4/fpm/php.ini

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# Verifikasi perubahan
php -i | grep -E "upload_max_filesize|post_max_size|max_execution_time"
```

**Alternatif: Jika menggunakan PHP 8.2 atau 8.3**

```bash
# Untuk PHP 8.2
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

# Atau untuk PHP 8.3 (ganti 8.2 dengan 8.3)
```

**⚠️ PENTING:**
- Pastikan `php-gd` extension sudah terinstall karena diperlukan oleh `phpoffice/phpspreadsheet` dan `maatwebsite/excel`
- Setelah install, verifikasi dengan: `php -m | grep gd`
- Restart PHP-FPM setelah install extensions: `sudo systemctl restart php8.4-fpm`

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

**⚠️ Sebelum install dependencies, pastikan semua PHP extensions sudah terinstall:**

```bash
# Verifikasi PHP extensions (pastikan gd, mbstring, xml, dll sudah ada)
php -m

# Jika extension belum terinstall, install terlebih dahulu
# Contoh untuk PHP 8.4:
sudo apt install -y php8.4-gd php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath

# Restart PHP-FPM setelah install extension
sudo systemctl restart php8.4-fpm  # atau php8.2-fpm sesuai versi PHP Anda
```

**Install PHP dependencies:**

```bash
# Jika terjadi error "lock file tidak kompatibel", coba update composer dulu
# Opsi 1: Install langsung (disarankan jika lock file sudah benar)
composer install --optimize-autoloader --no-dev

# Opsi 2: Jika masih error, update composer (hati-hati, akan update versi package)
# composer update --no-dev --optimize-autoloader

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
sudo chmod -R 755 /var/www/nasruladitri.space/serdadu
sudo chmod -R 775 /var/www/serdadu/nasruladitri.space/storage
sudo chmod -R 775 /var/www/nasruladitri.space/serdadu/bootstrap/cache
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

    # ⚠️ PENTING: Set max body size untuk upload file besar (misalnya Excel)
    # Default adalah 1M, kita set menjadi 100M untuk file Excel besar
    client_max_body_size 100M;

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
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;  # Sesuaikan dengan versi PHP (php8.2-fpm.sock atau php8.4-fpm.sock)
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # ⚠️ PENTING: Set timeout dan body size untuk upload file besar
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
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

### ⚠️ PENTING: Masalah Git Pull dengan Local Changes

**Masalah yang sering terjadi:**
Ketika melakukan `git pull` di server, sering muncul error:
```
error: Your local changes to the following files would be overwritten by merge:
- app/Http/Controllers/PublicDashboardController.php
- resources/views/layouts/dukcapil.blade.php
- resources/views/public/charts.blade.php
- resources/views/public/landing.blade.php
- routes/web.php
Please commit your changes or stash them before you merge.
```

**Penyebab:**
- File-file di server berubah karena proses build/cache Laravel
- File mungkin diubah langsung di server (manual edit)
- Git mendeteksi perubahan lokal yang tidak ter-commit

**Solusi:**

**Opsi 1: Gunakan Script Deployment (DISARANKAN)**

Gunakan script deployment yang sudah menangani masalah ini secara otomatis:

```bash
# Upload script deploy.sh ke server, lalu:
cd /var/www/nasruladitri.space/serdadu
chmod +x deploy.sh
sudo ./deploy.sh
```

Script ini akan:
- Otomatis menyimpan perubahan lokal (git stash) sebelum pull
- Menjalankan semua langkah deployment
- Mengembalikan perubahan jika diperlukan

**Opsi 2: Manual dengan Git Stash**

```bash
cd /var/www/nasruladitri.space/serdadu

# 1. Simpan perubahan lokal
git stash push -m "Backup sebelum pull $(date)"

# 2. Pull perubahan terbaru
git pull origin main

# 3. Install dependencies baru (jika ada)
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 4. Clear dan rebuild cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. Rebuild cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Run migrations (jika ada)
php artisan migrate --force

# 7. Restart services (sesuaikan versi PHP dengan yang digunakan)
sudo systemctl reload php8.4-fpm  # atau php8.2-fpm, php8.3-fpm, dll
sudo systemctl reload nginx
sudo supervisorctl restart serdadu-worker:*
```

**Opsi 3: Discard Local Changes (HATI-HATI)**

⚠️ **PERINGATAN:** Ini akan membuang SEMUA perubahan lokal yang tidak ter-commit!

```bash
cd /var/www/nasruladitri.space/serdadu

# 1. Buang semua perubahan lokal
git reset --hard HEAD
git clean -fd

# 2. Pull perubahan terbaru
git pull origin main

# 3. Lanjutkan dengan langkah deployment seperti biasa
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
sudo systemctl reload php8.4-fpm
sudo systemctl reload nginx
```

**Opsi 4: Gunakan Script Deploy-Safe (Auto Discard)**

Script `deploy-safe.sh` akan otomatis membuang perubahan lokal:

```bash
cd /var/www/nasruladitri.space/serdadu
chmod +x deploy-safe.sh
sudo ./deploy-safe.sh
```

### Cara Manual (Tanpa Script)

Jika tidak menggunakan script, ikuti langkah berikut:

```bash
cd /var/www/nasruladitri.space/serdadu

# Pull perubahan terbaru (dengan stash jika perlu)
git stash push -m "Backup sebelum pull" || true
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

# Restart services (sesuaikan versi PHP dengan yang digunakan)
sudo systemctl reload php8.4-fpm  # atau php8.2-fpm, php8.3-fpm, dll
sudo systemctl reload nginx
sudo supervisorctl restart serdadu-worker:*
```

---

## Script Deployment

### Setup Script Deployment

1. **Upload script ke server:**
   ```bash
   # Upload deploy.sh dan deploy-safe.sh ke /var/www/nasruladitri.space/serdadu/
   ```

2. **Beri permission execute:**
   ```bash
   cd /var/www/nasruladitri.space/serdadu
   chmod +x deploy.sh
   chmod +x deploy-safe.sh
   chmod +x quick-fix-git-pull.sh
   chmod +x fix-permissions.sh
   ```

3. **Edit script sesuai kebutuhan:**
   - Edit `APP_PATH` jika path berbeda
   - Edit `PHP_VERSION` sesuai versi PHP yang digunakan (8.2, 8.3, atau 8.4)

4. **Jalankan script:**
   ```bash
   # Quick fix untuk git pull error (interaktif)
   sudo ./quick-fix-git-pull.sh
   
   # Script deployment lengkap (menyimpan perubahan lokal)
   sudo ./deploy.sh
   
   # Script dengan auto-discard (membuang perubahan lokal)
   sudo ./deploy-safe.sh
   
   # Fix permissions saja
   sudo ./fix-permissions.sh
   ```

### Perbedaan Script

- **`quick-fix-git-pull.sh`**: Quick fix interaktif untuk masalah git pull (memilih stash atau discard)
- **`deploy.sh`**: Script deployment lengkap, menyimpan perubahan lokal dengan `git stash` sebelum pull (AMAN)
- **`deploy-safe.sh`**: Script deployment lengkap dengan auto-discard perubahan lokal menggunakan `git reset --hard` (HATI-HATI)
- **`fix-permissions.sh`**: Script untuk memperbaiki permissions Laravel

**Rekomendasi:**
- Untuk masalah git pull error: Gunakan `quick-fix-git-pull.sh` (interaktif, aman)
- Untuk deployment rutin: Gunakan `deploy.sh` (menyimpan perubahan lokal)
- Untuk deployment dengan discard: Gunakan `deploy-safe.sh` (hanya jika yakin perubahan lokal tidak penting)

---

## Troubleshooting

### ⚠️ Git Pull Error: Local Changes Would Be Overwritten

**Error yang muncul:**
```
error: Your local changes to the following files would be overwritten by merge:
- app/Http/Controllers/PublicDashboardController.php
- resources/views/layouts/dukcapil.blade.php
- resources/views/public/charts.blade.php
- resources/views/public/landing.blade.php
- routes/web.php
Please commit your changes or stash them before you merge.
Aborting
```

**Penyebab:**
- File di server berubah karena proses build/cache Laravel
- File mungkin diubah langsung di server
- Git mendeteksi perubahan lokal yang tidak ter-commit

**Solusi Cepat:**

**1. Stash perubahan lokal (DISARANKAN):**
```bash
cd /var/www/nasruladitri.space/serdadu
git stash push -m "Backup sebelum pull"
git pull origin main
# Lanjutkan deployment seperti biasa
```

**2. Discard perubahan lokal (HATI-HATI):**
```bash
cd /var/www/nasruladitri.space/serdadu
git reset --hard HEAD
git clean -fd
git pull origin main
# Lanjutkan deployment seperti biasa
```

**3. Gunakan script deployment:**
```bash
cd /var/www/nasruladitri.space/serdadu
sudo ./deploy.sh  # Atau deploy-safe.sh
```

**Pencegahan:**
- **JANGAN** edit file langsung di server. Edit di local, commit, push ke GitHub, lalu pull di server
- Gunakan script deployment yang otomatis menangani masalah ini
- Pastikan semua perubahan sudah di-commit dan push ke GitHub sebelum pull di server

**Catatan:**
- Perubahan karena build/cache biasanya tidak penting dan bisa dibuang
- Jika ada perubahan penting di server yang tidak ter-commit, backup terlebih dahulu sebelum discard

### ⚠️ Missing PHP Extensions (ext-gd, ext-mbstring, dll) - ERROR SAAT COMPOSER INSTALL

**Error yang muncul:**
- `Your lock file does not contain a compatible set of packages. Please run composer update.`
- `ext-gd * is missing from your system`
- `phpoffice/phpspreadsheet` requires `ext-gd`

**Solusi cepat:**

```bash
# 1. Cek versi PHP yang digunakan
php -v

# 2. Install extension yang diperlukan (contoh untuk PHP 8.4)
# Ganti 8.4 dengan versi PHP Anda (8.2, 8.3, dll)
sudo apt install -y php8.4-gd php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath php8.4-intl

# 3. Verifikasi extension sudah terinstall
php -m | grep -E "gd|mbstring|xml|curl|zip|bcmath|intl"

# 4. Restart PHP-FPM
sudo systemctl restart php8.4-fpm  # Sesuaikan dengan versi PHP Anda

# 5. Coba install composer lagi
cd /var/www/nasruladitri.space/serdadu  # atau path project Anda
composer install --optimize-autoloader --no-dev

# 6. Jika masih error dengan lock file, jalankan:
composer update --no-dev --optimize-autoloader
```

**Catatan penting:**
- Extension `gd` **WAJIB** terinstall untuk `phpoffice/phpspreadsheet` dan `maatwebsite/excel`
- Pastikan extension terinstall sebelum menjalankan `composer install`
- Setelah install extension, **restart PHP-FPM** agar perubahan berlaku
- Jika menggunakan PHP versi lain, ganti `8.4` dengan versi yang sesuai (contoh: `8.2`, `8.3`)

### ⚠️ Missing PHP Extensions di Windows/Laragon

**Error yang muncul:**
- `Your lock file does not contain a compatible set of packages. Please run composer update.`
- `ext-zip * is missing from your system`
- `phpoffice/phpspreadsheet` requires `ext-zip`

**Solusi untuk Windows/Laragon:**

1. **Buka file php.ini:**
   - Lokasi: `C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.ini`
   - (Sesuaikan path dengan versi PHP yang digunakan di Laragon)

2. **Cari baris extension zip (sekitar line 832):**
   ```ini
   ;extension=zip
   ```

3. **Hapus tanda titik koma (;) untuk mengaktifkan:**
   ```ini
   extension=zip
   ```

4. **Pastikan extension lain juga aktif:**
   - `extension=gd` (WAJIB untuk Excel processing)
   - `extension=mbstring`
   - `extension=curl`
   - `extension=intl`
   - `extension=zip`
   - `extension=xml` (biasanya sudah built-in di PHP 8.x)

5. **Restart Laragon:**
   - Klik menu Laragon → **Restart All**
   - Atau restart Apache/Nginx secara manual

6. **Verifikasi extension sudah aktif:**
   ```bash
   # Gunakan Laragon Terminal (yang sudah include PHP di PATH)
   php -m | findstr /i "zip gd mbstring curl intl"
   ```

7. **Jalankan composer install:**
   ```bash
   cd C:\laragon\www\serdadu
   composer install
   ```

**Catatan penting:**
- Extension `zip` dan `gd` **WAJIB** terinstall untuk `phpoffice/phpspreadsheet` dan `maatwebsite/excel`
- Di Laragon, extension biasanya sudah terinstall, hanya perlu diaktifkan di php.ini
- Setelah mengubah php.ini, **restart Laragon** agar perubahan berlaku
- Gunakan Laragon Terminal untuk menjalankan composer, karena PHP sudah ada di PATH

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

# PHP-FPM logs (sesuaikan versi PHP)
sudo tail -f /var/log/php8.4-fpm.log  # atau php8.2-fpm.log, php8.3-fpm.log
```

### Test PHP-FPM
```bash
# Ganti 8.2 dengan versi PHP Anda (8.4, 8.3, dll)
sudo systemctl status php8.4-fpm
sudo systemctl restart php8.4-fpm
```

### Test Nginx Configuration
```bash
sudo nginx -t
sudo systemctl status nginx
sudo systemctl restart nginx
```

### ⚠️ Error 413 Request Entity Too Large - File Upload Terlalu Besar

**Error:** `413 Request Entity Too Large` atau log error: `client intended to send too large body: X bytes`

**Penyebab:**
- File Excel yang diupload melebihi batas maksimum yang diizinkan
- Konfigurasi Nginx `client_max_body_size` terlalu kecil (default 1M)
- Konfigurasi PHP `upload_max_filesize` dan `post_max_size` terlalu kecil
- **PENTING:** Konfigurasi Nginx mungkin belum diterapkan di server block yang benar

**⚠️ Langkah Pertama - Cek File Konfigurasi yang Aktif:**

```bash
# Cek server block yang aktif untuk domain Anda
sudo nginx -T | grep -B 5 -A 15 "serdadu.nasruladitri.space"

# Atau cek semua file konfigurasi
sudo ls -la /etc/nginx/sites-available/
sudo ls -la /etc/nginx/sites-enabled/
```

**Solusi Lengkap:**

```bash
# 1. Edit konfigurasi Nginx untuk meningkatkan batas upload
sudo nano /etc/nginx/sites-available/serdadu
# atau
sudo nano /etc/nginx/sites-available/nasruladitri.space  # Sesuaikan dengan domain Anda

# Tambahkan atau ubah baris berikut di dalam block "server":
# client_max_body_size 100M;

# Simpan dan keluar (Ctrl+X, Y, Enter)

# 2. Edit konfigurasi PHP untuk meningkatkan batas upload
# Untuk PHP 8.4 FPM:
sudo nano /etc/php/8.4/fpm/php.ini

# Cari dan ubah nilai berikut (gunakan Ctrl+W untuk search):
# upload_max_filesize = 100M
# post_max_size = 100M
# max_execution_time = 300
# max_input_time = 300
# memory_limit = 256M

# Simpan dan keluar

# 3. Alternatif: Gunakan command untuk mengubah secara langsung
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_input_time = .*/max_input_time = 300/' /etc/php/8.4/fpm/php.ini

# 4. Test konfigurasi Nginx
sudo nginx -t

# 5. Restart services
sudo systemctl restart php8.4-fpm  # Sesuaikan dengan versi PHP
sudo systemctl restart nginx

# 6. Verifikasi konfigurasi PHP
php -i | grep -E "upload_max_filesize|post_max_size|max_execution_time"
```

**Catatan Penting:**
- `client_max_body_size` di Nginx harus lebih besar atau sama dengan `post_max_size` di PHP
- `post_max_size` di PHP harus lebih besar atau sama dengan `upload_max_filesize`
- Disarankan: `client_max_body_size = 100M`, `post_max_size = 100M`, `upload_max_filesize = 100M`
- Untuk file Excel yang sangat besar (>100M), sesuaikan nilai sesuai kebutuhan

**Verifikasi Setelah Perubahan:**

```bash
# Buat file PHP test untuk melihat konfigurasi
echo "<?php phpinfo(); ?>" | sudo tee /var/www/serdadu/public/phpinfo.php

# Akses di browser: http://yourdomain.com/phpinfo.php
# Cari: upload_max_filesize, post_max_size, max_execution_time

# HAPUS file phpinfo.php setelah verifikasi (untuk keamanan)
sudo rm /var/www/serdadu/public/phpinfo.php
```

**Troubleshooting Tambahan:**

Jika masih error setelah perubahan:

1. **Cek konfigurasi Nginx yang aktif:**
```bash
sudo nginx -T | grep client_max_body_size
```

2. **Cek konfigurasi PHP yang digunakan:**
```bash
php -i | grep -E "Configuration File|upload_max_filesize|post_max_size"
```

3. **Cek log error:**
```bash
# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM error log
sudo tail -f /var/log/php8.4-fpm.log
```

4. **Jika menggunakan domain spesifik, pastikan konfigurasi di server block yang benar:**
```bash
# List semua konfigurasi Nginx
ls -la /etc/nginx/sites-available/

# Edit konfigurasi untuk domain Anda
sudo nano /etc/nginx/sites-available/nasruladitri.space  # Ganti dengan domain Anda
```

---

## Checklist Deploy

- [ ] Semua file sudah di-commit dan push ke GitHub
- [ ] VPS sudah memiliki PHP 8.2+ (disarankan PHP 8.4), Composer, MySQL, Node.js, Nginx
- [ ] Semua PHP extensions sudah terinstall (gd, mbstring, xml, curl, zip, bcmath, intl)
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

