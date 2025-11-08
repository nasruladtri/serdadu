#!/bin/bash

# Script deployment AMAN untuk SERDADU
# Script ini menggunakan git reset untuk membuang perubahan lokal
# GUNAKAN HATI-HATI: Script ini akan membuang SEMUA perubahan lokal yang tidak ter-commit
# Jalankan di server VPS sebagai root atau user dengan sudo

set -e  # Exit on error

echo "=== SERDADU Safe Deployment Script ==="
echo "⚠️  WARNING: This script will DISCARD all local changes!"
echo ""
read -p "Are you sure you want to continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Deployment cancelled."
    exit 1
fi

# Path ke direktori aplikasi
APP_PATH="/var/www/nasruladitri.space/serdadu"

# Versi PHP (sesuaikan dengan versi yang digunakan)
PHP_VERSION="8.4"  # Ganti dengan 8.2, 8.3, atau 8.4 sesuai kebutuhan

# Masuk ke direktori aplikasi
cd "$APP_PATH"

echo "📁 Working directory: $APP_PATH"
echo ""

# 1. Cek status git
echo "1. Checking Git status..."
git status --short

# 2. Discard semua perubahan lokal
echo ""
echo "2. Discarding local changes..."
git reset --hard HEAD
git clean -fd
echo "   ✅ Local changes discarded"

# 3. Fetch dan pull perubahan terbaru
echo ""
echo "3. Fetching and pulling latest changes..."
git fetch origin
git pull origin main
echo "   ✅ Pull successful"

# 4. Install/update PHP dependencies
echo ""
echo "4. Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction
echo "   ✅ Composer dependencies installed"

# 5. Install/update Node.js dependencies
echo ""
echo "5. Installing Node.js dependencies..."
npm install --production
echo "   ✅ NPM dependencies installed"

# 6. Build assets untuk production
echo ""
echo "6. Building production assets..."
npm run build
echo "   ✅ Assets built successfully"

# 7. Clear Laravel cache
echo ""
echo "7. Clearing Laravel cache..."
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear
echo "   ✅ Cache cleared"

# 8. Rebuild cache untuk production
echo ""
echo "8. Rebuilding production cache..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
echo "   ✅ Production cache rebuilt"

# 9. Run migrations (jika ada)
echo ""
echo "9. Running database migrations..."
sudo -u www-data php artisan migrate --force
echo "   ✅ Migrations completed"

# 10. Fix permissions
echo ""
echo "10. Fixing permissions..."
sudo chown -R www-data:www-data "$APP_PATH"
sudo chmod -R 755 "$APP_PATH"
sudo chmod -R 775 "$APP_PATH/storage"
sudo chmod -R 775 "$APP_PATH/bootstrap/cache"
echo "   ✅ Permissions fixed"

# 11. Create storage link (jika belum ada)
echo ""
echo "11. Creating storage link..."
sudo -u www-data php artisan storage:link || echo "   ℹ️  Storage link already exists"
echo "   ✅ Storage link ready"

# 12. Restart services
echo ""
echo "12. Restarting services..."
sudo systemctl reload php${PHP_VERSION}-fpm
sudo systemctl reload nginx
# Restart queue worker jika ada
if sudo supervisorctl status serdadu-worker:* >/dev/null 2>&1; then
    sudo supervisorctl restart serdadu-worker:*
    echo "   ✅ Queue worker restarted"
fi
echo "   ✅ Services restarted"

# 13. Optimize autoloader
echo ""
echo "13. Optimizing autoloader..."
composer dump-autoload --optimize --no-dev
echo "   ✅ Autoloader optimized"

echo ""
echo "=== Deployment Complete! ==="
echo ""
echo "✅ All deployment steps completed successfully"
echo ""
echo "🌐 Your application should now be updated!"

