# Fix: Git Pull Error - Local Changes Would Be Overwritten

## Masalah

Ketika melakukan `git pull` di server, muncul error:
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

## Penyebab

1. File di server berubah karena proses build/cache Laravel
2. File mungkin diubah langsung di server (manual edit)
3. Git mendeteksi perubahan lokal yang tidak ter-commit

## Solusi Cepat

### Opsi 1: Quick Fix Script (DISARANKAN)

Gunakan script quick fix yang interaktif:

```bash
cd /var/www/nasruladitri.space/serdadu
chmod +x quick-fix-git-pull.sh
sudo ./quick-fix-git-pull.sh
```

Script ini akan:
- Menampilkan file yang berubah
- Memberikan pilihan: stash atau discard
- Melakukan git pull setelah itu

### Opsi 2: Manual - Stash Changes (AMAN)

```bash
cd /var/www/nasruladitri.space/serdadu

# Simpan perubahan lokal
git stash push -m "Backup sebelum pull"

# Pull perubahan terbaru
git pull origin main

# Jika perlu, kembalikan perubahan yang di-stash
# git stash pop  # Hanya jika perlu
```

### Opsi 3: Manual - Discard Changes (HATI-HATI)

⚠️ **PERINGATAN:** Ini akan membuang SEMUA perubahan lokal!

```bash
cd /var/www/nasruladitri.space/serdadu

# Buang semua perubahan lokal
git reset --hard HEAD
git clean -fd

# Pull perubahan terbaru
git pull origin main
```

### Opsi 4: Gunakan Deployment Script

Gunakan script deployment yang sudah menangani masalah ini:

```bash
cd /var/www/nasruladitri.space/serdadu
sudo ./deploy.sh  # Menyimpan perubahan lokal
# atau
sudo ./deploy-safe.sh  # Membuang perubahan lokal
```

## Pencegahan

1. **JANGAN edit file langsung di server**
   - Edit di local development
   - Commit dan push ke GitHub
   - Pull di server

2. **Gunakan script deployment**
   - Script otomatis menangani masalah ini
   - Lebih aman dan konsisten

3. **Pastikan semua perubahan sudah di-commit**
   - Sebelum pull di server, pastikan semua perubahan sudah di-push ke GitHub

## Catatan

- Perubahan karena build/cache biasanya tidak penting dan bisa dibuang
- Jika ada perubahan penting di server yang tidak ter-commit, backup terlebih dahulu sebelum discard
- Gunakan `git stash` untuk menyimpan perubahan jika tidak yakin apakah perubahan penting

## Tips

- Lihat perubahan yang di-stash: `git stash list`
- Apply perubahan yang di-stash: `git stash pop`
- Discard perubahan yang di-stash: `git stash drop`
- Lihat detail perubahan: `git stash show -p`

