#!/bin/bash

# Quick Fix untuk masalah Git Pull Error
# Script ini akan menyelesaikan masalah "local changes would be overwritten"
# Jalankan di server VPS

set -e

echo "=== Quick Fix: Git Pull Error ==="
echo ""

# Path ke direktori aplikasi
APP_PATH="/var/www/nasruladitri.space/serdadu"

cd "$APP_PATH"

echo "📁 Working directory: $APP_PATH"
echo ""

# Cek status git
echo "1. Checking Git status..."
if ! git diff --quiet || ! git diff --cached --quiet; then
    echo "   ⚠️  Local changes detected!"
    echo ""
    echo "   Files with changes:"
    git status --short
    echo ""
    
    # Tanyakan apa yang ingin dilakukan
    echo "Choose an action:"
    echo "  1) Stash changes (SAFE - saves changes)"
    echo "  2) Discard changes (WARNING - loses all local changes)"
    echo "  3) Cancel"
    echo ""
    read -p "Enter choice (1/2/3): " choice
    
    case $choice in
        1)
            echo ""
            echo "2. Stashing local changes..."
            git stash push -m "Backup sebelum pull $(date '+%Y-%m-%d %H:%M:%S')"
            echo "   ✅ Changes stashed successfully"
            ;;
        2)
            echo ""
            echo "⚠️  WARNING: This will discard ALL local changes!"
            read -p "Are you sure? (yes/no): " confirm
            if [ "$confirm" = "yes" ]; then
                echo ""
                echo "2. Discarding local changes..."
                git reset --hard HEAD
                git clean -fd
                echo "   ✅ Local changes discarded"
            else
                echo "   ❌ Cancelled"
                exit 1
            fi
            ;;
        3)
            echo "   ❌ Cancelled"
            exit 1
            ;;
        *)
            echo "   ❌ Invalid choice"
            exit 1
            ;;
    esac
else
    echo "   ✅ No local changes detected"
fi

# Pull perubahan terbaru
echo ""
echo "3. Pulling latest changes..."
git pull origin main
echo "   ✅ Pull successful"

echo ""
echo "=== Fix Complete! ==="
echo ""
echo "✅ Git pull successful!"
echo ""
echo "📝 Next steps:"
echo "   - Run deployment script: sudo ./deploy.sh"
echo "   - Or continue with manual deployment steps"
echo ""
echo "💡 Tips:"
echo "   - To view stashed changes: git stash list"
echo "   - To apply stashed changes: git stash pop"
echo "   - To discard stashed changes: git stash drop"

