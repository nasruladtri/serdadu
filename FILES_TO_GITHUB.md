# Daftar File/Folder untuk GitHub

Dokumen ini berisi daftar lengkap file dan folder yang **HARUS** ditambahkan ke GitHub untuk proyek SERDADU.

## 📁 Struktur Folder yang Harus di-Commit

```
serdadu/
│
├── 📁 app/                          ✅ HARUS
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   ├── Providers/
│   ├── Services/
│   └── View/
│       └── Components/
│
├── 📁 bootstrap/                    ✅ HARUS
│   ├── app.php
│   ├── cache/                       ⚠️ Struktur folder saja (kosong)
│   └── providers.php
│
├── 📁 config/                       ✅ HARUS
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── dukcapil_import.php
│   ├── excel.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
│
├── 📁 database/                     ✅ HARUS
│   ├── migrations/                  ✅ Semua file migration
│   ├── seeders/                     ✅ Semua file seeder
│   ├── factories/                   ✅ Model factories
│   └── dataset/                     ⚠️ Opsional (file Excel besar)
│
├── 📁 public/                       ✅ HARUS
│   ├── css/                         ✅ Jika ada file CSS custom
│   ├── img/                         ✅ Semua gambar
│   ├── map/                         ✅ File peta (GeoJSON, JS)
│   ├── index.php
│   ├── favicon.ico
│   ├── robots.txt
│   └── .htaccess
│
├── 📁 resources/                    ✅ HARUS
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/                       ✅ Semua file Blade
│       ├── auth/
│       ├── components/
│       ├── import/
│       ├── layouts/
│       ├── profile/
│       └── public/
│
├── 📁 routes/                       ✅ HARUS
│   ├── web.php
│   ├── auth.php
│   └── console.php
│
├── 📁 storage/                      ✅ HARUS (struktur saja)
│   ├── app/
│   │   ├── public/                  ⚠️ Folder kosong + .gitkeep
│   │   └── private/                 ⚠️ Folder kosong + .gitkeep
│   ├── framework/
│   │   ├── cache/                   ⚠️ Folder kosong + .gitkeep
│   │   ├── sessions/                ⚠️ Folder kosong + .gitkeep
│   │   ├── testing/                 ⚠️ Folder kosong + .gitkeep
│   │   └── views/                   ⚠️ Folder kosong + .gitkeep
│   └── logs/                        ⚠️ Folder kosong + .gitkeep
│
├── 📁 tests/                        ✅ HARUS
│   ├── Feature/
│   ├── Unit/
│   └── TestCase.php
│
├── 📄 artisan                       ✅ HARUS
├── 📄 composer.json                 ✅ HARUS
├── 📄 composer.lock                ✅ HARUS
├── 📄 package.json                  ✅ HARUS
├── 📄 package-lock.json             ✅ HARUS
├── 📄 vite.config.js                ✅ HARUS
├── 📄 phpunit.xml                   ✅ HARUS
├── 📄 .gitignore                    ✅ HARUS
├── 📄 README.md                     ✅ HARUS
├── 📄 DEPLOYMENT.md                 ✅ HARUS (panduan deploy)
└── 📄 FILES_TO_GITHUB.md            ✅ HARUS (file ini)
```

## ❌ File/Folder yang TIDAK Boleh di-Commit

### File Environment
- `.env`
- `.env.backup`
- `.env.production`
- `.env.local`
- Semua file `.env.*` (kecuali `.env.example` jika dibuat)

### Dependencies
- `vendor/` → Install dengan `composer install`
- `node_modules/` → Install dengan `npm install`

### Build & Cache Files
- `public/build/` → Dibuat saat `npm run build`
- `public/hot` → File development Vite
- `bootstrap/cache/*.php` → Compiled config files
- `storage/framework/cache/*` → Cache files
- `storage/framework/sessions/*` → Session files
- `storage/framework/views/*` → Compiled Blade views
- `storage/logs/*.log` → Log files

### Temporary Files
- `tmp_*.txt`
- `temp_*.txt`
- `*.log`
- `public/build.zip`
- `temp_gambar/`

### IDE & Editor Files
- `.vscode/`
- `.idea/`
- `.fleet/`
- `.zed/`
- `.nova/`
- `.phpactor.json`

### Testing & Development
- `.phpunit.cache/`
- `.phpunit.result.cache`
- `Homestead.json`
- `Homestead.yaml`

### Other
- `auth.json` (Composer auth)
- `npm-debug.log`
- `yarn-error.log`

## ⚠️ File Opsional (Pertimbangkan Ukuran)

### Database Dataset
- `database/dataset/*.xlsx` → File Excel besar, pertimbangkan untuk:
  - Tidak di-commit jika terlalu besar (>50MB)
  - Atau gunakan Git LFS (Large File Storage)
  - Atau upload manual ke server

## 📝 Checklist Sebelum Push ke GitHub

### 1. Pastikan .gitignore sudah benar
```bash
# File .gitignore sudah diupdate
cat .gitignore
```

### 2. Buat .gitkeep untuk folder storage kosong
```bash
# Di Windows (PowerShell atau Git Bash)
New-Item -ItemType File -Path "storage/app/public/.gitkeep" -Force
New-Item -ItemType File -Path "storage/app/private/.gitkeep" -Force
New-Item -ItemType File -Path "storage/framework/cache/.gitkeep" -Force
New-Item -ItemType File -Path "storage/framework/sessions/.gitkeep" -Force
New-Item -ItemType File -Path "storage/framework/testing/.gitkeep" -Force
New-Item -ItemType File -Path "storage/framework/views/.gitkeep" -Force
New-Item -ItemType File -Path "storage/logs/.gitkeep" -Force
```

### 3. Buat .env.example (jika belum ada)
```bash
# Copy .env ke .env.example
cp .env .env.example

# Edit .env.example, hapus nilai sensitif:
# - APP_KEY (biarkan kosong)
# - DB_PASSWORD (biarkan kosong atau contoh)
# - Semua password dan secret keys
```

### 4. Hapus file temporary
```bash
# Hapus file tmp_*.txt dan temp_*.txt
Remove-Item tmp_*.txt -ErrorAction SilentlyContinue
Remove-Item temp_*.txt -ErrorAction SilentlyContinue
Remove-Item public/build.zip -ErrorAction SilentlyContinue
```

### 5. Check status git
```bash
git status
# Pastikan hanya file yang diperlukan yang akan di-commit
```

### 6. Commit dan Push
```bash
git add .
git commit -m "Initial commit: SERDADU project"
git remote add origin https://github.com/USERNAME/serdadu.git
git push -u origin main
```

## 🔍 Verifikasi Setelah Push

Setelah push ke GitHub, verifikasi dengan:

1. **Clone repository baru** di folder lain untuk test:
```bash
cd /tmp
git clone https://github.com/USERNAME/serdadu.git test-clone
cd test-clone
```

2. **Check file yang di-commit**:
```bash
ls -la
# Pastikan tidak ada .env, vendor/, node_modules/
```

3. **Test install dependencies**:
```bash
composer install
npm install
# Pastikan tidak ada error
```

## 📊 Ukuran Repository

Setelah commit, repository seharusnya memiliki ukuran:
- **Tanpa dataset Excel**: ~5-20 MB
- **Dengan dataset Excel**: Bisa >50 MB (pertimbangkan Git LFS)

## 🚀 Setelah Push ke GitHub

Lanjutkan ke file `DEPLOYMENT.md` untuk panduan deploy ke VPS Ubuntu.

