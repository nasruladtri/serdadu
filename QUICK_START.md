# Quick Start - Deploy SERDADU ke VPS

## 📋 Ringkasan

Dokumen ini memberikan ringkasan cepat untuk deploy proyek SERDADU ke VPS Ubuntu menggunakan GitHub.

## 📚 Dokumentasi Lengkap

1. **FILES_TO_GITHUB.md** - Daftar lengkap file/folder yang harus di-commit ke GitHub
2. **DEPLOYMENT.md** - Panduan lengkap deploy ke VPS Ubuntu

## ⚡ Langkah Cepat

### 1. Persiapan di Local (Windows)

```powershell
# Pastikan .gitignore sudah benar
# File .gitignore sudah diupdate

# Buat .env.example (jika belum ada)
cp .env .env.example
# Edit .env.example, hapus nilai sensitif

# Hapus file temporary
Remove-Item tmp_*.txt -ErrorAction SilentlyContinue
Remove-Item temp_*.txt -ErrorAction SilentlyContinue
Remove-Item public/build.zip -ErrorAction SilentlyContinue

# Commit dan push ke GitHub
git add .
git commit -m "Initial commit: SERDADU project"
git remote add origin https://github.com/USERNAME/serdadu.git
git push -u origin main
```

### 2. Deploy di VPS Ubuntu

```bash
# Install dependencies
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd mysql-server nginx nodejs npm composer

# Clone repository
cd /var/www
sudo git clone https://github.com/USERNAME/serdadu.git
sudo chown -R $USER:$USER /var/www/serdadu
cd serdadu

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
nano .env  # Edit konfigurasi

# Setup database
sudo mysql -u root -p
# CREATE DATABASE serdadu_db;
# CREATE USER 'serdadu_user'@'localhost' IDENTIFIED BY 'password';
# GRANT ALL PRIVILEGES ON serdadu_db.* TO 'serdadu_user'@'localhost';
# FLUSH PRIVILEGES;
# EXIT;

php artisan migrate --force

# Setup permissions
sudo chown -R www-data:www-data /var/www/serdadu
sudo chmod -R 755 /var/www/serdadu
sudo chmod -R 775 /var/www/serdadu/storage
sudo chmod -R 775 /var/www/serdadu/bootstrap/cache
php artisan storage:link

# Setup Nginx (lihat DEPLOYMENT.md untuk konfigurasi lengkap)
sudo nano /etc/nginx/sites-available/serdadu
sudo ln -s /etc/nginx/sites-available/serdadu /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📝 Checklist

- [ ] File sudah di-commit dan push ke GitHub
- [ ] VPS sudah memiliki PHP 8.2+, MySQL, Nginx, Node.js
- [ ] Repository sudah di-clone
- [ ] Dependencies sudah di-install
- [ ] File .env sudah dikonfigurasi
- [ ] Database sudah dibuat dan migrations dijalankan
- [ ] Permissions sudah benar
- [ ] Nginx sudah dikonfigurasi
- [ ] Website bisa diakses

## 🔗 Referensi

Untuk detail lengkap, baca:
- **FILES_TO_GITHUB.md** - Daftar file untuk GitHub
- **DEPLOYMENT.md** - Panduan deploy lengkap

