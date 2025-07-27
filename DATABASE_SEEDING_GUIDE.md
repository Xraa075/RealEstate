# 🗂️ Database Seeding Guide

Panduan untuk menjalankan seeder database pada project Real Estate Management System.

## 📋 Apa yang Dibuat oleh Seeder

### 👥 Users (5 total)
- **1 Administrator**: `admin@realestate.com` / `admin123`
- **2 Managers**: 
  - `manager1@realestate.com` / `manager123`
  - `manager2@realestate.com` / `manager123`
- **2 Surveyors**:
  - `surveyor1@realestate.com` / `surveyor123`
  - `surveyor2@realestate.com` / `surveyor123`

### 🏠 Land Assets (4 total - sudah disetujui)
- **2 aset disetujui oleh Admin**
- **2 aset disetujui oleh Manager**

### 📝 Asset Requests (4 total - pending approval)
- **2 request dari Surveyor 1** (status: pending)
- **2 request dari Surveyor 2** (status: pending)

### 📊 Activity Logs
- Login activities untuk semua user
- Asset viewing activities
- Request submission activities

## 🚀 Cara Menjalankan Seeder

### 1. Pastikan Environment Siap
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Setup database connection di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=real_estate_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Jalankan Migration
```bash
php artisan migrate:fresh
```

### 3. Jalankan Seeder
```bash
# Jalankan semua seeder
php artisan db:seed

# Atau jalankan seeder khusus
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LandAssetSeeder
php artisan db:seed --class=AssetRequestSeeder
php artisan db:seed --class=ActivityLogSeeder
```

## ⚙️ Urutan Seeder yang Benar

Jika menjalankan manual, pastikan urutan ini:
1. `UserSeeder` (wajib pertama - untuk foreign keys)
2. `LandAssetSeeder` 
3. `AssetRequestSeeder`
4. `ActivityLogSeeder`

## 📁 File Seeder Utama

| File | Deskripsi |
|------|-----------|
| `DatabaseSeeder.php` | Seeder utama yang menjalankan semua seeder |
| `UserSeeder.php` | Membuat 5 user dengan role berbeda |
| `LandAssetSeeder.php` | Membuat 10 aset tanah yang sudah disetujui |
| `AssetRequestSeeder.php` | Membuat 6 request pending dari surveyor |
| `ActivityLogSeeder.php` | Membuat log aktivitas untuk demo |

## 🎯 Tips untuk Development

### Reset Database
```bash
# Reset dan seed ulang
php artisan migrate:fresh --seed
```

### Hanya User Saja
```bash
php artisan db:seed --class=UserSeeder
```

### Testing Login
Gunakan kredensial berikut untuk testing:

**Admin Dashboard:**
- Email: `admin@realestate.com`
- Password: `admin123`
- URL: `/admin/dashboard`

**Manager Dashboard:**
- Email: `manager1@realestate.com` 
- Password: `manager123`
- URL: `/manager/dashboard`

**Surveyor Dashboard:**
- Email: `surveyor1@realestate.com`
- Password: `surveyor123`  
- URL: `/surveyor/dashboard`

## ❗ Catatan Penting

### Asset Documents
- **Tidak ada seeder untuk asset documents** - tim dapat menambah manual
- Ini mencegah konflik file dan path issues
- Fokus seeder hanya pada data struktur utama

### Data Geometry
- Semua asset menggunakan sample geometry Jakarta
- Format: GeoJSON Polygon
- Dapat diubah sesuai kebutuhan project

### Status Asset
- `tersedia` - Dapat dijual/disewakan
- `disewakan` - Sedang dalam kontrak sewa
- `terjual` - Sudah terjual
- `dalam_sengketa` - Ada masalah legal

## 🔧 Troubleshooting

### Error Foreign Key
Pastikan UserSeeder dijalankan pertama:
```bash
php artisan db:seed --class=UserSeeder
```

### Error Migration
```bash
php artisan migrate:status
php artisan migrate:fresh
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

**Happy Coding! 🎉**

Jika ada pertanyaan atau masalah dengan seeder, hubungi team lead atau check dokumentasi di `bisnis_proses.mdc`.
