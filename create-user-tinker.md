# Cara Membuat Akun User di PHP Artisan Tinker

## Metode 1: Cara Sederhana (Password otomatis di-hash)

```php
use App\Models\User;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => 'password123',
]);
```

**Catatan:** Karena User model sudah memiliki cast `'password' => 'hashed'`, password akan otomatis di-hash.

## Metode 2: Menggunakan Hash::make() (Lebih Eksplisit)

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
]);
```

## Metode 3: Dengan Email Verified

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'email_verified_at' => now(),
]);
```

## Metode 4: Menggunakan Factory (Untuk Testing)

```php
use App\Models\User;

// Membuat 1 user dengan factory
User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com',
]);

// Membuat beberapa user sekaligus
User::factory()->count(5)->create();
```

## Langkah-langkah di Tinker:

1. Buka tinker:
   ```bash
   php artisan tinker
   ```

2. Copy salah satu metode di atas dan paste di tinker

3. Tekan Enter

4. User akan dibuat dan akan menampilkan object User yang baru dibuat

## Contoh Lengkap:

```php
php artisan tinker

>>> use App\Models\User;
>>> use Illuminate\Support\Facades\Hash;
>>> 
>>> $user = User::create([
...     'name' => 'Administrator',
...     'email' => 'admin@serdadu.local',
...     'password' => Hash::make('admin123'),
...     'email_verified_at' => now(),
... ]);
>>> 
>>> $user
=> App\Models\User {#1234
     id: 1,
     name: "Administrator",
     email: "admin@serdadu.local",
     email_verified_at: "2025-10-11 08:00:00",
     created_at: "2025-10-11 08:00:00",
     updated_at: "2025-10-11 08:00:00",
   }
```

## Verifikasi User Dibuat:

```php
// Cek semua user
User::all();

// Cek user tertentu
User::where('email', 'admin@example.com')->first();

// Hitung jumlah user
User::count();
```


