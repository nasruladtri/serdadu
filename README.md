# Serdadu Project
(Sistem Rekap Data Terpadu)

## About
SERDADU (Sistem Rekap Data Terpadu) is a web application built with Laravel framework. This project serves as [describe your project's main purpose and features].

## Requirements
- PHP >= 8.0
- Composer
- MySQL/MariaDB
- Node.js & NPM

## Installation
1. Clone the repository
```bash
git clone https://github.com/yourusername/serdadu.git
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up database
- Create database in MySQL
- Configure database credentials in .env file
- Run migrations:
```bash
php artisan migrate
```

## Running the Application
```bash
php artisan serve
npm run dev
```

## Features
- Ringkasan wilayah memuat nama kabupaten, jumlah kecamatan, serta jumlah desa/kelurahan dalam format tabel ringkas untuk memberi konteks cakupan data 
- Kartu statistik penduduk merinci total populasi, pembagian laki-laki dan perempuan, serta jumlah wajib KTP (termasuk rinciannya per gender) agar pengguna cepat memahami komposisi demografis
- Peta interaktif Leaflet menampilkan persebaran penduduk Kabupaten Madiun lengkap dengan opsi basemap (Default, Light, Dark, Satellite) dan kontrol layer di sudut kanan atas
- Dropdown filter kecamatan memungkinkan pengguna memusatkan peta dan statistik ke area pilihan, sekaligus memicu pembaruan legend serta label desa/kelurahan secara dinamis
- Setiap batas wilayah (kabupaten, kecamatan, desa/kelurahan) diberi styling dan label otomatis di peta, termasuk badge bernomor pada legend untuk memudahkan identifikas
- Hover dan popup pada fitur kecamatan/desa menonjolkan area yang dipilih sekaligus menampilkan statistik Laki-laki/Perempuan/Total; interaksi ini memanfaatkan fungsi highlight dan reset agar pengalaman eksplorasi terasa responsif

## Security
-

## License
-
