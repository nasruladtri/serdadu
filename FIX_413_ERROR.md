# 🚨 Solusi Cepat: Error 413 Request Entity Too Large

## Masalah
Error `413 Request Entity Too Large` saat upload file Excel di halaman import.

## ⚡ Solusi Cepat (Copy-Paste Langsung)

Jalankan command berikut di VPS Anda (sesuaikan dengan versi PHP):

### Untuk PHP 8.4:

```bash
# 1. Update konfigurasi PHP untuk upload file besar
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_input_time = .*/max_input_time = 300/' /etc/php/8.4/fpm/php.ini

# 2. Update konfigurasi Nginx
# Cek file konfigurasi Nginx yang aktif
sudo ls -la /etc/nginx/sites-available/

# Edit file konfigurasi (ganti dengan nama file Anda, contoh: nasruladitri.space)
sudo nano /etc/nginx/sites-available/nasruladitri.space

# Di dalam block "server", tambahkan atau ubah baris:
# client_max_body_size 100M;
# (Tambahkan setelah baris "root /var/www/...")

# Simpan: Ctrl+X, Y, Enter

# 3. Test konfigurasi Nginx
sudo nginx -t

# 4. Restart services
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx

# 5. Verifikasi
php -i | grep -E "upload_max_filesize|post_max_size"
```

### Untuk PHP 8.2 atau 8.3:

Ganti `8.4` dengan `8.2` atau `8.3` di command di atas.

---

## 📝 Langkah Manual (Jika Command Otomatis Tidak Bekerja)

### 1. Edit Konfigurasi PHP

```bash
# Edit PHP.ini untuk FPM
sudo nano /etc/php/8.4/fpm/php.ini
```

Cari (gunakan `Ctrl+W`) dan ubah nilai berikut:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

Simpan: `Ctrl+X`, `Y`, `Enter`

### 2. Edit Konfigurasi Nginx

```bash
# Cari file konfigurasi Nginx untuk domain Anda
sudo ls -la /etc/nginx/sites-available/

# Edit file konfigurasi (contoh: nasruladitri.space)
sudo nano /etc/nginx/sites-available/nasruladitri.space
```

Di dalam block `server { ... }`, tambahkan atau ubah:

```nginx
server {
    listen 80;
    server_name serdadu.nasruladitri.space;
    root /var/www/nasruladitri.space/serdadu/public;
    
    # ⚠️ TAMBAHKAN BARIS INI
    client_max_body_size 100M;
    
    # ... konfigurasi lainnya
}
```

Di dalam block `location ~ \.php$ { ... }`, tambahkan:

```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    
    # ⚠️ TAMBAHKAN BARIS INI
    fastcgi_read_timeout 300;
    fastcgi_send_timeout 300;
}
```

Simpan: `Ctrl+X`, `Y`, `Enter`

### 3. Test dan Restart

```bash
# Test konfigurasi Nginx
sudo nginx -t

# Jika sukses, restart services
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

### 4. Verifikasi

```bash
# Cek konfigurasi PHP
php -i | grep -E "upload_max_filesize|post_max_size"

# Seharusnya menampilkan:
# upload_max_filesize => 100M => 100M
# post_max_size => 100M => 100M
```

---

## 🔍 Troubleshooting

### Jika masih error setelah perubahan:

1. **Cek konfigurasi Nginx yang aktif:**
```bash
sudo nginx -T | grep client_max_body_size
```

2. **Cek file konfigurasi mana yang digunakan:**
```bash
# List semua file konfigurasi
ls -la /etc/nginx/sites-available/
ls -la /etc/nginx/sites-enabled/

# Pastikan file yang benar di-enable
sudo ls -la /etc/nginx/sites-enabled/
```

3. **Cek log error:**
```bash
# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM error log
sudo tail -f /var/log/php8.4-fpm.log
```

4. **Cek versi PHP yang digunakan:**
```bash
php -v
```

5. **Pastikan menggunakan file php.ini yang benar:**
```bash
# Cek file php.ini yang digunakan
php -i | grep "Configuration File"
```

---

## ✅ Checklist

Setelah melakukan perubahan, pastikan:

- [ ] `upload_max_filesize` di PHP.ini = 100M
- [ ] `post_max_size` di PHP.ini = 100M
- [ ] `client_max_body_size` di Nginx = 100M
- [ ] PHP-FPM sudah di-restart
- [ ] Nginx sudah di-restart
- [ ] Konfigurasi Nginx sudah di-test (`sudo nginx -t`)
- [ ] Tidak ada error di log

---

## 📞 Jika Masih Bermasalah

1. **Cek ukuran file Excel:**
   - Jika file > 100M, tingkatkan nilai di atas sesuai kebutuhan

2. **Cek memory limit:**
   - Pastikan `memory_limit` cukup besar (minimal 256M)

3. **Cek disk space:**
   ```bash
   df -h
   ```

4. **Cek permissions:**
   ```bash
   ls -la /var/www/nasruladitri.space/serdadu/storage/app/
   ```

---

## 🎯 Contoh Konfigurasi Lengkap

### File: `/etc/nginx/sites-available/nasruladitri.space`

```nginx
server {
    listen 80;
    server_name serdadu.nasruladitri.space;
    root /var/www/nasruladitri.space/serdadu/public;

    # ⚠️ PENTING: Untuk upload file besar
    client_max_body_size 100M;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

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

### File: `/etc/php/8.4/fpm/php.ini`

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

---

**Setelah melakukan perubahan di atas, error 413 seharusnya sudah teratasi!** ✅

