# 🧩 Serdadu Project

**(Sistem Rekap Data Terpadu)**
Website: [https://magangtiumpo.my.id](https://magangtiumpo.my.id)

## 📖 Tentang Proyek

**SERDADU (Sistem Rekap Data Terpadu)** merupakan aplikasi web yang dikembangkan menggunakan **framework Laravel**.
Website ini dirancang untuk membantu **Dinas Kependudukan dan Pencatatan Sipil (Disdukcapil)** dalam melakukan **pengelolaan, rekapitulasi, dan visualisasi data penduduk secara terpadu dan efisien**.

Dengan adanya SERDADU, proses pengumpulan data, pelaporan statistik, dan monitoring perkembangan penduduk di wilayah kerja dapat dilakukan secara **otomatis, akurat, dan real-time**.

## ⚙️ Spesifikasi Sistem

* **PHP** >= 8.0
* **Composer**
* **MySQL / MariaDB**
* **Node.js & NPM**

## 🚀 Langkah Instalasi

1. **Clone Repository**

   ```bash
   git clone https://github.com/yourusername/serdadu.git
   ```

2. **Instal Dependensi**

   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Lingkungan (.env)**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Pengaturan Database**

   * Buat database baru di MySQL
   * Atur kredensial database di file `.env`
   * Jalankan migrasi database:

     ```bash
     php artisan migrate
     ```

## ▶️ Menjalankan Aplikasi

Untuk menjalankan aplikasi di server lokal:

```bash
php artisan serve
npm run dev
```

Kemudian buka di browser: [http://localhost:8000](http://localhost:8000)

## 🌟 Fitur Utama

* 📊 **Rekapitulasi Data Penduduk Terpadu**
  Menampilkan data penduduk dari berbagai sumber dalam satu sistem yang mudah diakses.
* 📈 **Statistik dan Visualisasi Data**
  Menyajikan grafik dan laporan statistik secara dinamis.
* 🧍‍♂️ **Manajemen Data Individu & Keluarga**
  Fitur untuk menambah, mengubah, atau menghapus data kependudukan.
* 🔒 **Autentikasi & Hak Akses Pengguna**
  Sistem login dan role user (admin, operator, tamu).
* 🗂️ **Ekspor & Cetak Laporan**
  Mendukung ekspor data ke format Excel dan PDF.

## 🤝 Kontribusi

Kontribusi sangat terbuka!
Silakan buat *pull request* atau ajukan *issue* untuk pengembangan lebih lanjut. Pastikan kode mengikuti standar Laravel dan memiliki dokumentasi yang jelas.

## 🛡️ Keamanan

Jika menemukan masalah keamanan pada sistem SERDADU, jangan tulis di *issue tracker*.
Silakan laporkan secara langsung melalui email: **[email protected]** (ganti dengan email kamu).

## 📜 Lisensi

Proyek ini dilisensikan di bawah **MIT License** — silakan gunakan, modifikasi, dan kembangkan dengan menyertakan atribusi yang sesuai.

---
