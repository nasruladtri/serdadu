#!/bin/bash

# Quick Fix untuk Route Cache Error
# Script ini akan clear dan rebuild route cache
# Jalankan di server VPS sebagai root atau user dengan sudo

set -e

echo "=== Fix Route Cache Error ==="
echo ""

# Path ke direktori aplikasi
APP_PATH="/var/www/nasruladitri.space/serdadu"

cd "$APP_PATH"

echo "📁 Working directory: $APP_PATH"
echo ""

# 1. Clear route cache
echo "1. Clearing route cache..."
sudo -u www-data php artisan route:clear
echo "   ✅ Route cache cleared"

# 2. Clear config cache (karena route mungkin ter-cache di config)
echo ""
echo "2. Clearing config cache..."
sudo -u www-data php artisan config:clear
echo "   ✅ Config cache cleared"

# 3. Clear view cache
echo ""
echo "3. Clearing view cache..."
sudo -u www-data php artisan view:clear
echo "   ✅ View cache cleared"

# 4. Clear application cache
echo ""
echo "4. Clearing application cache..."
sudo -u www-data php artisan cache:clear
echo "   ✅ Application cache cleared"

# 5. Rebuild route cache untuk production
echo ""
echo "5. Rebuilding route cache..."
sudo -u www-data php artisan route:cache
echo "   ✅ Route cache rebuilt"

# 6. Rebuild config cache untuk production
echo ""
echo "6. Rebuilding config cache..."
sudo -u www-data php artisan config:cache
echo "   ✅ Config cache rebuilt"

# 7. Rebuild view cache untuk production
echo ""
echo "7. Rebuilding view cache..."
sudo -u www-data php artisan view:cache
echo "   ✅ View cache rebuilt"

# 8. List routes untuk verifikasi
echo ""
echo "8. Verifying routes..."
if sudo -u www-data php artisan route:list | grep -q "public.compare"; then
    echo "   ✅ Route 'public.compare' found!"
else
    echo "   ⚠️  Route 'public.compare' not found in route list"
    echo "   This might indicate the route is not properly defined"
fi

echo ""
echo "=== Fix Complete! ==="
echo ""
echo "✅ Route cache has been cleared and rebuilt"
echo ""
echo "🌐 Your website should now work correctly!"
echo ""
echo "💡 If the error persists:"
echo "   1. Check routes/web.php to ensure route is defined"
echo "   2. Check PublicDashboardController to ensure compare() method exists"
echo "   3. Run: sudo -u www-data php artisan route:list | grep compare"

