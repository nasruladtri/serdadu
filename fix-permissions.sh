#!/bin/bash

# Script untuk memperbaiki permission Laravel setelah git pull
# Jalankan di server VPS sebagai root

echo "=== Memperbaiki Permission Laravel ==="

# Path ke direktori aplikasi
APP_PATH="/var/www/nasruladitri.space/serdadu"

# 1. Set ownership ke www-data
echo "1. Setting ownership ke www-data..."
sudo chown -R www-data:www-data "$APP_PATH"

# 2. Set permission untuk seluruh direktori
echo "2. Setting permission 755 untuk direktori aplikasi..."
sudo chmod -R 755 "$APP_PATH"

# 3. Set permission 775 untuk storage dan bootstrap/cache (writable)
echo "3. Setting permission 775 untuk storage dan bootstrap/cache..."
sudo chmod -R 775 "$APP_PATH/storage"
sudo chmod -R 775 "$APP_PATH/bootstrap/cache"

# 4. Pastikan file .gitkeep ada di storage (jika perlu)
echo "4. Memastikan struktur storage ada..."
mkdir -p "$APP_PATH/storage/app/public"
mkdir -p "$APP_PATH/storage/framework/cache/data"
mkdir -p "$APP_PATH/storage/framework/sessions"
mkdir -p "$APP_PATH/storage/framework/testing"
mkdir -p "$APP_PATH/storage/framework/views"
mkdir -p "$APP_PATH/storage/logs"
mkdir -p "$APP_PATH/bootstrap/cache"

# 5. Set permission lagi setelah membuat direktori
sudo chmod -R 775 "$APP_PATH/storage"
sudo chmod -R 775 "$APP_PATH/bootstrap/cache"

# 6. Clear cache Laravel
echo "5. Clearing Laravel cache..."
cd "$APP_PATH"
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear

# 7. Rebuild cache (optional, untuk production)
echo "6. Rebuilding cache untuk production..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 8. Pastikan storage link ada
echo "7. Membuat storage link..."
sudo -u www-data php artisan storage:link

echo ""
echo "=== Selesai! ==="
echo "Permission sudah diperbaiki. Silakan refresh browser Anda."

