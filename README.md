# 🚗 WOTO - Sistem Penjualan Mobil Bekas Showroom

Sistem manajemen lengkap untuk showroom penjualan mobil bekas yang membantu mengelola inventori kendaraan, mencatat riwayat biaya kendaraan (service, spare parts, maintenance) dengan approval workflow, mengelola perhitungan kredit kendaraan dengan leasing integration, mengelola penerimaan pembayaran dengan sistem kwitansi otomatis, dan audit trail lengkap untuk semua operasional bisnis.

**Version 1.17.0** - File Upload Management System

## ✨ Fitur Utama

### 📊 Dashboard Overview Real-time
- **4 Metric Cards Modern**: Vehicles Sold, Total Sales, Ready for Sale, Total Cost dengan design responsive
- **Advanced Card Features**: Horizontal layout dengan icon di kanan, hover animations, compact design
- **Business Intelligence**: Real-time operational overview untuk decision making
- **Responsive Grid Layout**: 4 columns desktop → 2 columns tablet → 1 column mobile

### 🚗 Manajemen Kendaraan Lengkap
- **CRUD Vehicles**: Sistem lengkap Create, Read, Update, Delete kendaraan
- **Advanced Form Features**:
  - **Rich Text Editor (Quill)**: Editor deskripsi kendaraan dengan formatting lengkap
  - **Auto-formatting**: Otomatis format nomor polisi Indonesia, kilometer, harga, cylinder capacity
  - **Progress Indicator**: Visual progress pengisian form dengan step-by-step guide
  - **Form State Persistence**: Auto-save form ke localStorage, restore saat kembali
  - **Keyboard Shortcuts**: Ctrl+S (save), Ctrl+R (reset), Escape (back)
  - **Conditional Validation**: Field selling date/price muncul otomatis berdasarkan status
  - **Smart Dropdowns**: Cascading select untuk Brand → Type → Model
  - **File Upload**: Upload STNK dengan preview dan validasi
- **Spesifikasi Lengkap**: Police number, year, fuel type, kilometer, purchase/selling price, dll
- **Status Tracking**: Available/Sold dengan conditional fields
- **🛠️ Checklist Kelengkapan Kendaraan**: Sistem pencatatan kelengkapan peralatan kendaraan
  - **Item Kelengkapan**: STNK Asli, Kunci Roda, Ban Serep, Kunci Serep, Dongkrak
  - **Visual Status**: Indikator warna hijau (tersedia) dan merah (tidak tersedia)
  - **Database Integration**: Tersimpan di tabel vehicle_equipment dengan type purchase/sales
  - **Auto-default**: STNK Asli otomatis dicentang sebagai default
- **Audit Trail**: Activity logging lengkap untuk semua perubahan kendaraan
- **📊 Analisis Harga Jual**: Card "Rincian Modal Mobil" dengan analisis komprehensif
  - **Perhitungan Modal**: Total modal = harga beli + biaya kendaraan (approved + pending)
  - **Validasi Harga Display**: Cek apakah harga jual mencakup total modal
  - **Perbandingan Harga**: Bandingkan display_price vs selling_price (harga actual terjual)
  - **Margin Keuntungan**: Hitung margin keuntungan untuk display dan actual price
  - **Rekomendasi Pricing**: Saran harga minimum untuk mencapai breakeven point
  - **Status Badge**: Visual indicator untuk cost approval (Approved/Pending/Rejected)
  - **Paginasi**: Sistem paginasi untuk cost records dengan 10 items per halaman
  - **Gap Analysis**: Analisis selisih antara harga display vs harga actual terjual
- **💰 Perhitungan Kredit**: Sistem manajemen perhitungan kredit kendaraan
  - **Loan Calculation CRUD**: Create, Read, Update, Delete perhitungan kredit
  - **Leasing Integration**: Relasi dengan tabel leasing untuk data perusahaan pembiayaan
  - **Advanced Form Interface**: Modal form dengan validasi lengkap dan error handling
  - **Audit Trail**: Activity logging lengkap untuk semua perubahan loan calculations
  - **Loan Calculation Audit Trail**: Dedicated audit page dengan filtering berdasarkan vehicle
  - **Advanced Filtering**: Search, vehicle filter, dan pagination untuk audit trail
  - **Sorting by Leasing Name**: Data diurutkan berdasarkan nama leasing secara alfabetis
  - **Permission-based Access**: vehicle-loan-calculation.* permissions untuk kontrol akses
  - **Database Integration**: Foreign key ke vehicles dan leasings table
  - **Real-time Updates**: Auto-refresh data setelah create/update/delete operations
- **💳 Pembayaran Pembelian**: Sistem manajemen pembayaran pembelian kendaraan
  - **Purchase Payment CRUD**: Create, Read, Update, Delete pembayaran pembelian dengan multiple file upload
  - **Multiple File Upload**: Upload multiple dokumen pembayaran dengan auto-naming dan storage management
  - **Auto Payment Number**: Generate nomor pembayaran otomatis dengan format 0001/PP/WOTO/XII/2025
  - **Advanced Form Interface**: Modal form dengan validasi lengkap dan error handling
  - **Purchase Price Validation**: Prevent overpayment melebihi harga beli kendaraan
  - **File Type Icons**: Display icon berdasarkan tipe file (PDF, JPG, PNG) dengan nama file
  - **Audit Trail**: Activity logging lengkap untuk semua perubahan purchase payments
  - **Purchase Payment Audit Trail**: Dedicated audit page dengan filtering berdasarkan vehicle
  - **Advanced Filtering**: Search by payment number/description/user, vehicle filter, pagination
  - **Permission-based Access**: vehicle-purchase-payment.* permissions untuk kontrol akses
  - **Database Integration**: Foreign key ke vehicles table dengan document management
  - **Real-time Updates**: Auto-refresh data setelah create/update/delete operations

- **📋 Tanda Terima BPKB**: Sistem manajemen tanda terima BPKB kendaraan
  - **Certificate Receipt CRUD**: Create, Read, Update, Delete tanda terima BPKB dengan interface lengkap
  - **Auto Certificate Number Generation**: Generate nomor tanda terima otomatis dengan format 001/TT/BPKB/WOTO/XII/2025
  - **Comprehensive Form Fields**: BPKB A/N, Faktur Asli A/N, Fotocopy KTP A/N, Blanko Kwitansi, NIK, Form A, Surat Pelepasan Hak, Lain-lain
  - **Advanced Form Interface**: Modal form dengan validasi lengkap dan error handling
  - **Single Receipt Rule**: Sistem mencegah pembuatan lebih dari satu tanda terima per kendaraan
  - **Certificate Receipt File Upload**: Upload multiple berkas tanda terima dengan validasi lengkap (PDF, JPG, JPEG, PNG, max 2MB)
  - **File Type Icons**: Display icon berdasarkan tipe file dengan nama file lengkap
  - **Certificate Receipt Audit Trail**: Dedicated audit page dengan filtering berdasarkan vehicle
  - **Advanced Filtering**: Search by certificate number/in_the_name_of/user, vehicle filter, pagination
  - **Print Certificate Receipt**: Generate PDF tanda terima BPKB otomatis dalam format landscape
  - **Dual Version PDF**: ORIGINAL dan COPY dalam satu halaman A4 landscape yang compact
  - **Professional PDF Layout**: Template dengan logo perusahaan, data lengkap, dan signature sections
  - **Permission-based Access**: vehicle-registration-certificate-receipt.* permissions untuk kontrol akses
  - **Database Integration**: Foreign key ke vehicles table dengan relationship management
  - **Audit Trail**: Activity logging lengkap untuk semua perubahan certificate receipts
  - **Real-time Updates**: Auto-refresh data setelah create/update/delete operations

- **💰 Penerimaan Pembayaran**: Sistem manajemen penerimaan pembayaran penjualan kendaraan
  - **Payment Receipt CRUD**: Create, Read, Update, Delete penerimaan pembayaran dengan multiple file upload
  - **Multiple File Upload**: Upload multiple dokumen penerimaan dengan auto-naming dan storage management
  - **Auto Payment Number**: Generate nomor penerimaan otomatis dengan format 001/PR/WOTO/I/2025
  - **Advanced Form Interface**: Modal form dengan validasi lengkap dan error handling
  - **Selling Price Validation**: Prevent overpayment melebihi harga jual kendaraan
  - **Settlement Date**: Field tanggal harus diselesaikan ketika pembayaran belum lunas
  - **Remaining Balance**: Otomatis hitung sisa pembayaran yang harus dilunasi
  - **File Type Icons**: Display icon berdasarkan tipe file (PDF, JPG, PNG) dengan nama file
  - **Print Receipt**: Generate kwitansi PDF otomatis untuk penerimaan pembayaran
  - **Terbilang Rupiah**: Konversi angka ke teks bahasa Indonesia (satu juta lima ratus ribu rupiah)
  - **Audit Trail**: Activity logging lengkap untuk semua perubahan payment receipts
  - **Payment Receipt Audit Trail**: Dedicated audit page dengan filtering berdasarkan vehicle
  - **Advanced Filtering**: Search by payment number/description/user, vehicle filter, pagination
  - **Permission-based Access**: vehicle-payment-receipt.* permissions untuk kontrol akses
  - **Database Integration**: Foreign key ke vehicles table dengan document management
  - **Real-time Updates**: Auto-refresh data setelah create/update/delete operations

- **📝 Berita Acara Serah Terima**: Sistem manajemen berita acara serah terima kendaraan
  - **Handover CRUD**: Create, Read, Update, Delete berita acara serah terima dengan interface lengkap
  - **Auto Handover Number Generation**: Generate nomor berita acara otomatis dengan format 001/BAST/WOTO/XII/2025
  - **Comprehensive Form Fields**: Tanggal, Serah Terima Dari, Kepada, Yang Menyerahkan, Yang Menerima
  - **Advanced Form Interface**: Modal form dengan validasi lengkap dan error handling
  - **Single Handover Rule**: Sistem mencegah pembuatan lebih dari satu berita acara per kendaraan
  - **Handover File Upload**: Upload multiple berkas berita acara dengan validasi lengkap (PDF, JPG, JPEG, PNG, max 2MB)
  - **File Type Icons**: Display icon berdasarkan tipe file dengan nama file lengkap
  - **Payment Completion Conditional**: Section handover hanya muncul setelah pembayaran lunas
  - **Print Handover**: Generate PDF berita acara serah terima otomatis
  - **Audit Trail**: Activity logging lengkap untuk semua perubahan handovers
  - **Permission-based Access**: vehicle-handover.* permissions untuk kontrol akses
  - **Database Integration**: Foreign key ke vehicles table dengan document management
  - **Real-time Updates**: Auto-refresh data setelah create/update/delete operations

### 📋 Master Data Management
- **Manajemen Brand**: Database merek mobil populer di Indonesia (31+ brand)
- **Manajemen Vendor**: Database vendor/supplier kendaraan
- **Manajemen Salesmen**: Database salesman dengan auto-create user account dan status management
- **Manajemen Models**: Database model kendaraan STNK Indonesia (SEDAN, SUV, MPV, MINIBUS, TRUCK, dll)
- **Manajemen Categories**: Database kategori kendaraan STNK Indonesia (MB, BB, BA, BK, TK, DS, dll)
- **Manajemen Types**: Database 65+ tipe kendaraan Indonesia dengan format STNK (SIGRA 1.0 D MT, dll)
- **Kategori & Tipe**: Pengelompokan kendaraan berdasarkan kategori dan tipe
- **Lokasi Warehouse**: Penyimpanan kendaraan di berbagai lokasi gudang
- **Foto Kendaraan**: Upload dan manajemen gambar kendaraan

### 💰 Sistem Costs (Biaya Kendaraan)
- **Cost Management**: Sistem lengkap pencatatan berbagai biaya kendaraan (service, spare parts, maintenance, dll)
- **Advanced Form Features**:
  - **Vendor Selection**: Dropdown vendor/supplier dengan database lengkap
  - **Auto-formatting Price**: Otomatis format harga Rupiah (150.000) dengan thousand separator
  - **Document Upload**: Upload invoice, kwitansi, atau dokumen biaya
  - **Approval Workflow**: Sistem approval pending/approved/rejected untuk cost records
- **Complete Cost Records**: Tanggal biaya, vendor, deskripsi, total price, document, status
- **Audit Trail**: Activity logging lengkap untuk semua perubahan cost records
- **Vendor Relationship**: Setiap cost record terkait dengan vendor tertentu
- **Status Tracking**: Pending/Approved/Rejected dengan workflow approval
- **Status Badges**: Visual indicator warna untuk status cost (Green=Approved, Yellow=Pending, Red=Rejected)
- **Export Features**: Excel dan PDF dengan template yang konsisten

### 💎 Sistem Komisi (Commission Management)
- **Commission Management**: Sistem lengkap manajemen komisi kendaraan (komisi penjualan dan pembelian)
- **Advanced Form Features**:
  - **Commission Types**: Komisi Penjualan (Sales) dan Komisi Pembelian (Purchase)
  - **Auto-formatting Amount**: Format mata uang Rupiah otomatis dengan thousand separator
  - **Modal Form Interface**: Form create/edit dengan validasi lengkap dan error handling
  - **Date Picker**: Input tanggal komisi dengan validasi
- **Complete Commission Records**: Tanggal komisi, tipe (sales/purchase), deskripsi, jumlah, status
- **Audit Trail**: Activity logging lengkap untuk semua perubahan commission records
- **Vehicle Relationship**: Setiap komisi terkait dengan kendaraan tertentu
- **Commission Tables**: Separate tables untuk komisi penjualan (hijau) dan pembelian (biru)
- **Modal Confirmation**: Confirmation dialog untuk delete operations dengan detail komisi
- **Commission Audit Trail**: Dedicated audit page dengan filtering berdasarkan vehicle dan tipe komisi
- **Advanced Filtering**: Filter berdasarkan vehicle, commission type, search functionality
- **Export Features**: Excel dan PDF dengan template yang konsisten
- **Real-time Updates**: Auto-refresh commission data setelah create/update/delete

### 👥 Manajemen User & Akses
- **Role-Based Access Control**: Sistem permission yang fleksibel
- **Multi-User**: Mendukung berbagai level user (Admin, Manager, Staff)
- **Activity Logging**: Tracking semua aktivitas user dalam sistem

### 🧾 Sistem Kwitansi Penjualan
- **Cetak Kwitansi Otomatis**: Generate kwitansi PDF untuk kendaraan yang sudah terjual
- **Form Data Pembeli**: Modal input data lengkap pembeli (nama, telepon, alamat) sebelum cetak
- **Nomor Kwitansi Otomatis**: Format KW/YYYYMMDD/XXXXX dengan sequence per tahun
- **Terbilang Rupiah**: Konversi angka ke teks dalam bahasa Indonesia (satu juta lima ratus ribu rupiah)
- **Logo Perusahaan**: Integrasi logo perusahaan dari database ke kwitansi PDF
- **Data Perusahaan Dinamis**: Informasi perusahaan (nama, alamat, telepon, email) diambil dari tabel companies
- **Format Kuitansi Formal**: Template A4 portrait dengan layout profesional
- **Base64 Image Support**: Logo ditampilkan menggunakan base64 encoding untuk kompatibilitas DomPDF
- **Layout Responsif**: Header dengan logo kiri, title tengah, informasi perusahaan terstruktur

### 📊 Laporan & Analytics
- **Dashboard Overview v1.10.0**: Real-time business dashboard dengan 4 metric cards modern
  - **Vehicles Sold This Month**: Jumlah kendaraan terjual bulan ini dengan tracking quantity
  - **Total Sales This Month**: Revenue total dari penjualan kendaraan bulan ini dengan currency formatting
  - **Vehicles Ready for Sale**: Jumlah kendaraan yang tersedia dijual + info kendaraan baru bulan ini
  - **Total Cost This Month**: Total biaya operasional bulan ini dengan expense tracking
- **Advanced Card Features**: Horizontal layout, hover animations, compact design, color-coded icons
- **Responsive Grid Layout**: 4 columns desktop → 2 columns tablet → 1 column mobile
- **Transition Optimization**: Fixed flickering issues dengan transition-shadow dan transition-transform
- **Business Intelligence**: Comprehensive operational overview untuk decision making
- **Export Data**: Export laporan ke Excel dan PDF dengan template yang konsisten
- **Audit Trail**: Riwayat lengkap semua perubahan data (Brands, Vendors, Categories, Types, Vehicles, Costs, dll)
- **Activity Logging**: Tracking detail perubahan dengan before/after values
- **PDF Reports**: Template PDF yang rapi dan konsisten untuk semua module

### 🔒 Backup & Security
- **Automated Backup**: Sistem backup otomatis database dan file
- **Restore**: Fitur restore data dari backup
- **Data Security**: Enkripsi dan proteksi data sensitif

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x - PHP Framework
- **Frontend**: Livewire 3.x + Flux UI Components + Alpine.js
- **Database**: MySQL/PostgreSQL dengan Eloquent Relationships
- **Authentication**: Laravel Sanctum
- **File Storage**: Laravel Storage (local/cloud)
- **PDF Generation**: DomPDF (dengan base64 image support untuk logo perusahaan, landscape orientation untuk certificate receipts)
- **Excel Export**: Laravel Excel (Maatwebsite)
- **Activity Logging**: Spatie Laravel Activity Log (audit trail lengkap)
- **Permissions**: Spatie Laravel Permission (role-based access)
- **Rich Text Editor**: Quill.js untuk deskripsi kendaraan
- **UI/UX**: Tailwind CSS + Custom styling untuk dark mode compatibility
- **JavaScript**: Vanilla JS dengan Livewire integration, Quill.js editor, localStorage API
- **Form Enhancement**: Auto-formatting, progress indicators, keyboard shortcuts, state persistence
- **Multiple File Upload**: Livewire WithFileUploads untuk upload multiple files dengan comma-separated storage
- **Auto Numbering**: Custom auto-increment numbering system dengan format 0001/PP/WOTO/XII/2025, 001/TT/BPKB/WOTO/XII/2025, dan 001/BAST/WOTO/XII/2025
- **Database Transactions**: Atomic operations untuk multi-table updates dengan error handling dan rollback
- **Indonesian Text Conversion**: Custom terbilang helper function untuk mata uang Rupiah
- **Receipt Generation**: Sistem generate kwitansi PDF dengan nomor otomatis dan data perusahaan dinamis
- **File Upload Management**: Advanced file upload system dengan multiple file support, auto-cleanup, dan type validation
- **Data Seeding**: Laravel Seeders untuk data master (Brands, Types, Vehicles, Companies)

## 📋 Prasyarat Sistem

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Git

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/your-username/woto.git
cd woto
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit file `.env` dan konfigurasikan database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=woto
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Migration & Seeding
```bash
php artisan migrate
php artisan db:seed
```

### Seeder Data Tersedia:
- **BrandSeeder**: 31+ merek mobil Indonesia
- **VendorSeeder**: 25+ vendor/supplier kendaraan Indonesia
- **VehicleModelSeeder**: 25+ model kendaraan STNK Indonesia (SEDAN, SUV, MPV, MINIBUS, TRUCK, dll)
- **CategorySeeder**: 26+ kategori kendaraan STNK Indonesia (MB, BB, BA, BK, TK, DS, dll)
- **TypeSeeder**: 65+ tipe kendaraan dengan format STNK
- **CompanySeeder**: Data perusahaan default untuk kwitansi (nama, alamat, telepon, email, logo)
- **VehicleSeeder**: Sample data kendaraan untuk testing (relasi ke brands, vendors, types, warehouses)
- **UserSeeder**: User default untuk testing
- **WarehouseSeeder**: Data warehouse default

Untuk seed seeder tertentu:
```bash
php artisan db:seed --class=BrandSeeder
php artisan db:seed --class=VendorSeeder
php artisan db:seed --class=CompanySeeder
php artisan db:seed --class=VehicleSeeder
php artisan db:seed --class=VehicleModelSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=TypeSeeder
```

### 6. Build Assets
```bash
npm run build
# atau untuk development
npm run dev
```

### 7. Storage Link
```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 📖 Penggunaan

### Akses Sistem
1. Buka browser dan akses `http://localhost:8000`
2. Login dengan akun default:
   - **Email**: admin@woto.com
   - **Password**: password

### Menu Utama
- **Dashboard**: Overview bisnis dan statistik
- **Vehicles**: Manajemen inventori kendaraan lengkap dengan CRUD + Commission Management + Loan Calculation Management + Audit Trail
- **Costs**: Manajemen biaya kendaraan (service, spare parts, maintenance) + Approval Workflow + Audit Trail
- **Commissions**: Audit trail lengkap untuk semua aktivitas komisi kendaraan + Vehicle Filtering
- **Loan Calculations**: Audit trail lengkap untuk semua aktivitas perhitungan kredit kendaraan + Vehicle Filtering
- **Purchase Payments**: Audit trail lengkap untuk semua aktivitas pembayaran pembelian kendaraan + Vehicle Filtering
- **Payment Receipts**: Audit trail lengkap untuk semua aktivitas penerimaan pembayaran kendaraan + Vehicle Filtering
- **Certificate Receipts**: Audit trail lengkap untuk semua aktivitas tanda terima BPKB kendaraan + Vehicle Filtering
- **Brands**: Manajemen merek mobil (31+ brand Indonesia) + Audit Trail
- **Vendors**: Manajemen vendor/supplier kendaraan + Audit Trail
- **Salesmen**: Manajemen salesman dengan auto-create user account + Status management + Audit Trail
- **Models**: Manajemen model kendaraan STNK (25+ model: SEDAN, SUV, MPV, MINIBUS, dll) + Audit Trail
- **Categories**: Manajemen kategori kendaraan STNK (26+ kategori) + Audit Trail
- **Types**: Manajemen tipe kendaraan (65+ tipe dengan format STNK) + Brand filtering + Audit Trail
- **Warehouses**: Manajemen lokasi penyimpanan kendaraan
- **Users**: Manajemen user dan role
- **Backup & Restore**: Manajemen backup data

### Fitur Khusus Models Module
- **STNK Classification**: Model kendaraan berdasarkan klasifikasi STNK Indonesia (SEDAN, SUV, MPV, MINIBUS, TRUCK, dll)
- **Vehicle Relationship**: Setiap model memiliki relasi dengan kendaraan
- **Audit Trail**: Tracking lengkap perubahan dengan before/after values
- **Export**: Export ke Excel/PDF dengan filter aktif
- **UI Consistency**: Interface mengikuti pola BrandAudit dengan card-based layout

### Cara Menggunakan Models Module
1. **Akses Models**: Klik menu "Models" di sidebar
2. **Add New Model**: Klik "Create Vehicle Model" untuk menambah model baru
3. **Edit Model**: Klik icon edit untuk mengubah model
4. **View Details**: Klik icon eye untuk melihat detail dan vehicles terkait
5. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan
6. **Export Data**: Gunakan tombol Excel/PDF untuk export data

### Fitur Khusus Categories Module
- **STNK Classification**: Kategori berdasarkan klasifikasi STNK Indonesia (MB, BB, BA, BK, TK, DS, dll)
- **Vehicle Relationship**: Setiap kategori memiliki relasi dengan kendaraan
- **Audit Trail**: Tracking lengkap perubahan dengan before/after values
- **Export**: Export ke Excel/PDF dengan filter aktif
- **UI Consistency**: Interface mengikuti pola BrandAudit dengan card-based layout

### Cara Menggunakan Categories Module
1. **Akses Categories**: Klik menu "Categories" di sidebar
2. **Add New Category**: Klik "Create Category" untuk menambah kategori baru
3. **Edit Category**: Klik icon edit untuk mengubah kategori
4. **View Details**: Klik icon eye untuk melihat detail dan vehicles terkait
5. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan
6. **Export Data**: Gunakan tombol Excel/PDF untuk export data

### Fitur Khusus Types Module
- **Brand Relationship**: Setiap type terkait dengan brand tertentu
- **Advanced Filtering**: Filter berdasarkan brand dengan tombol clear filters
- **STNK Format**: Data types menggunakan format STNK Indonesia (SIGRA 1.0 D MT)
- **Audit Trail**: Tracking lengkap perubahan dengan before/after values
- **Export**: Export ke Excel/PDF dengan filter aktif
- **UI Consistency**: Interface mengikuti pola BrandAudit dengan card-based layout

### Cara Menggunakan Types Module
1. **Akses Types**: Klik menu "Types" di sidebar
2. **Filter by Brand**: Gunakan dropdown Brand untuk filter types tertentu
3. **Add New Type**: Klik "Create Type" untuk menambah type baru
4. **Edit Type**: Klik icon edit untuk mengubah type
5. **View Details**: Klik icon eye untuk melihat detail dan vehicles terkait
6. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan
7. **Export Data**: Gunakan tombol Excel/PDF untuk export data

### 🚗 Fitur Khusus Vehicles Module
- **Complete CRUD Operations**: Create, Read, Update, Delete kendaraan dengan interface lengkap
- **Advanced Form Features**:
  - **Quill Rich Text Editor**: Editor deskripsi dengan toolbar lengkap (bold, italic, lists, links, dll)
  - **Auto-formatting Fields**:
    - **Police Number**: Format otomatis ke format Indonesia (BG 1821 MY)
    - **Kilometer**: Format ribuan dengan titik (15.000)
    - **Purchase/Selling Price**: Format mata uang Indonesia (150.000.000)
    - **Cylinder Capacity**: Format angka otomatis (1.500)
  - **Smart Progress Indicator**: Visual progress dengan 5 langkah pengisian form
  - **Form State Persistence**: Auto-save ke localStorage setiap 30 detik, restore saat kembali
  - **Keyboard Shortcuts**: Ctrl+S (simpan), Ctrl+R (reset), Escape (kembali)
  - **Conditional Fields**: Selling date/price muncul otomatis saat status "Sold"
  - **Cascading Dropdowns**: Brand → Type → Model dengan filter otomatis
  - **File Upload STNK**: Upload gambar dengan preview dan validasi
- **Comprehensive Specifications**: Police number, year, fuel type (Bensin/Solar), kilometer, cylinder capacity, colors, dll
- **Status Management**: Available/Sold dengan validasi conditional
- **Audit Trail**: Activity logging lengkap dengan before/after values
- **Export Features**: Excel dan PDF dengan template yang konsisten
- **Responsive UI**: Interface modern dengan grouping visual dan icons
- **📊 Analisis Harga Jual**: Card "Rincian Modal Mobil" dengan analisis komprehensif
  - **Perhitungan Modal**: Total modal = harga beli + biaya kendaraan (approved + pending)
  - **Validasi Harga Display**: Cek apakah harga jual mencakup total modal
  - **Perbandingan Harga**: Bandingkan display_price vs selling_price (harga actual terjual)
  - **Margin Keuntungan**: Hitung margin keuntungan untuk display dan actual price
  - **Rekomendasi Pricing**: Saran harga minimum untuk mencapai breakeven point
  - **Status Badge**: Visual indicator untuk cost approval (Approved/Pending/Rejected)
  - **Paginasi**: Sistem paginasi untuk cost records dengan 10 items per halaman
   - **Gap Analysis**: Analisis selisih antara harga display vs harga actual terjual

### 🚗 Fitur Khusus Receipt/Kwitansi Module
- **PDF Receipt Generation**: Generate kwitansi penjualan kendaraan dalam format PDF A4 portrait
- **Buyer Information Form**: Modal form untuk input data pembeli sebelum generate kwitansi
- **Auto Receipt Number**: Nomor kwitansi otomatis dengan format KW/YYYYMMDD/XXXXX (sequential per tahun)
- **Indonesian Currency Converter**: Fungsi terbilang lengkap untuk mata uang Rupiah (satu juta lima ratus ribu rupiah)
- **Company Logo Integration**: Logo perusahaan dari database ditampilkan di header kwitansi
- **Dynamic Company Data**: Informasi perusahaan (nama, alamat, telepon, email, website) diambil dari tabel companies
- **Professional Layout**: Template kwitansi formal dengan layout yang rapi dan profesional
- **DomPDF Compatible**: Optimasi untuk PDF generation dengan base64 image encoding
- **Complete Transaction Data**: Menampilkan data kendaraan, pembeli, salesman, dan detail transaksi

### Cara Menggunakan Receipt/Kwitansi Module
1. **Akses Vehicles**: Klik menu "Vehicles" di sidebar
2. **Pilih Kendaraan Terjual**: Cari kendaraan dengan status "Sold" (Terjual)
3. **Cetak Kwitansi**: Klik tombol "Print Receipt" pada card vehicle detail
4. **Input Data Pembeli**: Isi form modal dengan data lengkap pembeli:
   - **Nama Pembeli**: Nama lengkap pembeli kendaraan
   - **Nomor Telepon**: Kontak telepon pembeli
   - **Alamat Pembeli**: Alamat lengkap pembeli
5. **Generate PDF**: Klik "Cetak Kwitansi" untuk generate dan download PDF
6. **Nomor Kwitansi Otomatis**: Sistem otomatis generate nomor kwitansi dengan format KW/YYYYMMDD/XXXXX
7. **Terbilang Rupiah**: Jumlah terjual otomatis dikonversi ke teks bahasa Indonesia

### Format Kwitansi Generated
```
┌─────────────────────────────────────────────────┐
│ [🏢 LOGO]         KWITANSI                      │ ← Logo kiri, title tengah
│                    Penjualan Kendaraan Bermotor   │
│                                                 │
│                    PT. ABC MOTOR                │ ← Nama perusahaan (dari DB)
│                    Jl. Sudirman No. 123         │ ← Alamat perusahaan
│                    Phone: (021) 1234567         │ ← Kontak perusahaan
│                    Email: info@abc.com          │
│                                                 │
│   No. KW/20251110/00001                    [CAP]│ ← Nomor kwitansi
├─────────────────┬───────────────────────────────┤
│ DATA KENDARAAN  │ DATA PEMBELI                  │ ← Table layout
│ No. Polisi: ... │ Nama: ...                     │
│ Merk/Type: ...  │ Telepon: ...                  │
│ Tahun: ...      │ Alamat: ...                   │
│ No. Rangka: ... │                               │
│ No. Mesin: ...  │                               │
│ Warna: ...      │                               │
├─────────────────┴───────────────────────────────┤
│                                                 │
│ TELAH TERIMA DARI                               │ ← Section pembayaran
│ [Nama Pembeli]                                  │
│                                                 │
│ UANG SEJUMLAH                                   │
│ Rp 1,500,000                                    │
│ Satu juta lima ratus ribu rupiah               │ ← Terbilang otomatis
│                                                 │
│ UNTUK PEMBAYARAN                                │
│ Pembelian Kendaraan Bermotor                     │
│ No. Polisi: ..., Merk/Type: ...                 │
├─────────────────────────────────────────────────┤
│ Penerima,          Yang Menyerahkan,    Hormat Kami, │ ← Tanda tangan
│ ________________   ________________     ________________ │
│ (Nama Pembeli)     (Nama Salesman)      (Nama Manager)  │
└─────────────────────────────────────────────────┘
```

### Cara Menggunakan Vehicles Module
1. **Akses Vehicles**: Klik menu "Vehicles" di sidebar
2. **View All Vehicles**: Lihat daftar kendaraan dengan pagination dan search
3. **Add New Vehicle**: Klik "Create Vehicle" untuk menambah kendaraan baru
   - **Progress Indicator**: Ikuti panduan 5 langkah pengisian form
   - **Auto-formatting**: Input otomatis terformat sesuai standar Indonesia
   - **Rich Text Editor**: Gunakan Quill untuk deskripsi detail
   - **Smart Dropdowns**: Pilih Brand → Type otomatis terfilter → Model
   - **File Upload**: Upload gambar STNK dengan preview
   - **Form Persistence**: Form tersimpan otomatis, bisa dilanjutkan kapan saja
   - **Completeness Checklist**: Centang item kelengkapan kendaraan (STNK Asli, Kunci Roda, Ban Serep, dll)
4. **Edit Vehicle**: Klik icon edit untuk mengubah data kendaraan
   - **Completeness Management**: Update checklist kelengkapan kendaraan sesuai kondisi aktual
5. **View Details**: Klik icon eye untuk melihat detail lengkap kendaraan
   - **Kelengkapan Kendaraan**: Lihat status kelengkapan peralatan kendaraan dengan indikator visual
   - **Equipment Summary**: Ringkasan jumlah item tersedia vs tidak tersedia
   - **Analisis Harga Jual**: Lihat card "Rincian Modal Mobil" dengan analisis komprehensif
   - **Perhitungan Modal**: Total modal = harga beli + semua biaya kendaraan
   - **Validasi Pricing**: Cek apakah harga display mencukupi untuk breakeven
   - **Perbandingan Harga**: Bandingkan display price vs actual selling price
   - **Rekomendasi**: Dapatkan saran harga minimum dan margin keuntungan
   - **Status Cost**: Lihat status approval setiap cost record (Approved/Pending/Rejected)
   - **Paginasi**: Navigasi cost records dengan sistem paginasi
7. **Purchase Payments**: Kelola pembayaran pembelian kendaraan
   - **View Purchase Payments**: Lihat tabel pembayaran pembelian dengan file icons
   - **Add Purchase Payment**: Klik "Tambah" untuk menambah pembayaran baru (hanya jika belum lunas)
   - **Upload Multiple Files**: Upload multiple dokumen (PDF, JPG, PNG) dengan auto-naming
   - **Auto Payment Number**: Nomor pembayaran otomatis dengan format 0001/PP/WOTO/XII/2025
   - **Price Validation**: Sistem mencegah pembayaran melebihi harga beli kendaraan
   - **Edit/Delete**: Update atau hapus pembayaran dengan permission-based access
8. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan lengkap
9. **Export Data**: Gunakan tombol Excel/PDF untuk export data kendaraan
9. **Keyboard Shortcuts**:
   - **Ctrl+S**: Simpan form
   - **Ctrl+R**: Reset form (dengan konfirmasi modal)
   - **Escape**: Kembali ke halaman sebelumnya

### Form Fields Vehicles
- **Informasi Dasar**: Police number (auto-format), year (dropdown 15 tahun), brand, type, model
- **Detail Kendaraan**: Chassis number, engine number, color, fuel type (Bensin/Solar)
- **Spesifikasi Teknis**: Kilometer (auto-format), cylinder capacity (auto-format)
- **Registrasi & Dokumen**: Vehicle registration date/expiry, STNK file upload
- **Kelengkapan Kendaraan**: Checklist equipment (STNK Asli, Kunci Roda, Ban Serep, Kunci Serep, Dongkrak)
- **Informasi Keuangan**: Purchase date, purchase price (auto-format), selling price (conditional), status (Available/Sold)

### 💰 Fitur Khusus Costs Module
- **Complete Cost Management**: Sistem lengkap pencatatan berbagai biaya kendaraan (service, spare parts, maintenance, dll)
- **Advanced Form Features**:
  - **Vendor Dropdown**: Pilih vendor/supplier dari database lengkap
  - **Auto-formatting Price**: Format harga Rupiah otomatis (150.000) dengan thousand separator
  - **Document Upload**: Upload invoice, kwitansi, atau dokumen biaya (Max 5MB, PDF/JPG/PNG)
  - **Approval Workflow**: Sistem approval dengan status Pending/Approved/Rejected
- **Comprehensive Cost Records**: Cost date, vendor, description, total price, document, status, created by
- **Vendor Integration**: Setiap cost record terkait dengan vendor tertentu melalui foreign key
- **Status Management**: Approval workflow dengan conditional actions (approve/reject)
- **Audit Trail**: Activity logging lengkap dengan before/after values untuk semua perubahan
- **Advanced Filtering**: Filter berdasarkan status, vendor, dan vehicle dengan clear filters
- **Export Features**: Excel dan PDF dengan template yang konsisten dan filter aktif
- **Responsive UI**: Interface modern dengan visual grouping dan icons

### Cara Menggunakan Costs Module
1. **Akses Costs**: Klik menu "Costs" di sidebar
2. **View All Records**: Lihat daftar cost records dengan pagination, search, dan filtering
3. **Add New Cost Record**: Klik "Create Cost Record" untuk menambah record baru
   - **Vendor Selection**: Pilih vendor dari dropdown yang tersedia
   - **Auto-formatting**: Input harga otomatis terformat dengan thousand separator
   - **Document Upload**: Upload dokumen pendukung dengan preview
   - **Approval Workflow**: Record akan berstatus "Pending" sampai di-approve
4. **Edit Cost Record**: Klik icon edit untuk mengubah data (hanya status "Pending")
5. **View Details**: Klik icon eye untuk melihat detail lengkap cost record
6. **Approval Actions**: Gunakan tombol Approve/Reject untuk mengubah status record
7. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan lengkap
8. **Export Data**: Gunakan tombol Excel/PDF untuk export data dengan filter aktif
9. **Advanced Filtering**:
   - **Status Filter**: Filter berdasarkan Pending/Approved/Rejected
   - **Vehicle Filter**: Filter berdasarkan kendaraan tertentu
   - **Vendor Filter**: Filter berdasarkan vendor tertentu
   - **Clear Filters**: Reset semua filter dengan satu klik

### Form Fields Costs
- **Vehicle Selection**: Pilih kendaraan dari dropdown (hanya yang status Available)
- **Cost Date**: Tanggal biaya (tidak boleh di masa depan)
- **Vendor**: Pilih vendor/supplier dari database lengkap
- **Description**: Deskripsi detail biaya yang dikeluarkan
- **Total Price**: Biaya total (auto-format Rupiah dengan thousand separator)
- **Document**: Upload file pendukung (invoice, kwitansi, dll)
- **Status**: Pending/Approved/Rejected (managed by approval workflow)
- **Created By**: User yang membuat record (auto-fill)

### Approval Workflow
- **Pending**: Status default untuk record baru, bisa di-edit dan dihapus
- **Approved**: Record telah disetujui, tidak bisa di-edit lagi
- **Rejected**: Record ditolak, masih bisa di-edit untuk diperbaiki

### 💎 Cara Menggunakan Commission Module
1. **Akses Commissions**: Klik menu "Commissions" di sidebar untuk melihat audit trail
2. **View Commission Tables**: Di halaman vehicle detail, lihat tabel komisi penjualan dan pembelian
3. **Add New Commission**: Klik "Tambah Komisi" untuk menambah komisi baru (max 4 per vehicle)
   - **Commission Type**: Pilih Komisi Penjualan atau Komisi Pembelian
   - **Date**: Pilih tanggal komisi dengan date picker
   - **Description**: Masukkan deskripsi komisi (required)
   - **Amount**: Masukkan jumlah komisi (auto-format Rupiah)
4. **Edit Commission**: Klik icon edit pada tabel komisi untuk mengubah data
5. **Delete Commission**: Klik icon trash untuk menghapus dengan confirmation modal
6. **Commission Audit Trail**: Klik "Audit" di halaman vehicle detail untuk melihat riwayat lengkap
7. **Advanced Filtering**: Gunakan search, vehicle filter, dan commission type filter
8. **Export Data**: Gunakan tombol Excel/PDF untuk export data commission audit

### Form Fields Commissions
- **Commission Type**: Komisi Penjualan / Komisi Pembelian
- **Commission Date**: Tanggal komisi dengan validasi
- **Description**: Deskripsi detail komisi (required, max 255 characters)
- **Amount**: Jumlah komisi (auto-format Rupiah dengan thousand separator)
- **Vehicle**: Kendaraan terkait (auto-assigned berdasarkan context)

### 💰 Cara Menggunakan Loan Calculations Module
1. **Akses Loan Calculations**: Klik menu "Loan Calculations" di sidebar untuk melihat audit trail
2. **View Loan Calculation Tables**: Di halaman vehicle detail, lihat tabel perhitungan kredit
3. **Add New Loan Calculation**: Klik "Tambah" untuk menambah perhitungan kredit baru
   - **Leasing Selection**: Pilih perusahaan leasing dari dropdown
   - **Description**: Masukkan deskripsi perhitungan kredit (required)
4. **Edit Loan Calculation**: Klik icon edit pada tabel untuk mengubah data
5. **Delete Loan Calculation**: Klik icon trash untuk menghapus dengan confirmation modal
6. **Loan Calculation Audit Trail**: Klik "Audit" di halaman vehicle detail untuk melihat riwayat lengkap
7. **Advanced Filtering**: Gunakan search, vehicle filter, dan pagination
8. **Export Data**: Gunakan tombol Excel/PDF untuk export data loan calculation audit

### Form Fields Loan Calculations
- **Leasing**: Pilih perusahaan leasing dari database lengkap
- **Description**: Deskripsi detail perhitungan kredit (required, max 255 characters)
- **Vehicle**: Kendaraan terkait (auto-assigned berdasarkan context)

### 💳 Cara Menggunakan Purchase Payment Audit Trail
1. **Akses Purchase Payments**: Klik menu "Purchase Payments" di sidebar untuk melihat audit trail
2. **View Purchase Payment Tables**: Di halaman vehicle detail, lihat tabel pembayaran pembelian
3. **Add New Purchase Payment**: Klik "Tambah" untuk menambah pembayaran pembelian baru
   - **Multiple File Upload**: Upload multiple dokumen (PDF, JPG, PNG) sekaligus
   - **Auto Payment Number**: Nomor pembayaran otomatis dengan format 0001/PP/WOTO/XII/2025
   - **Price Validation**: Sistem mencegah pembayaran melebihi harga beli kendaraan
   - **File Type Icons**: Icon otomatis berdasarkan tipe file dengan nama lengkap
4. **Edit Purchase Payment**: Klik icon edit pada tabel untuk mengubah data
5. **Delete Purchase Payment**: Klik icon trash untuk menghapus dengan confirmation modal
6. **Purchase Payment Audit Trail**: Klik "Audit" di halaman vehicle detail untuk melihat riwayat lengkap
7. **Advanced Filtering**: Gunakan search, vehicle filter, dan pagination
8. **Export Data**: Gunakan tombol Excel/PDF untuk export data purchase payment audit

### Form Fields Purchase Payments
- **Payment Date**: Tanggal pembayaran dengan validasi (required)
- **Description**: Deskripsi pembayaran (required, max 255 characters)
- **Amount**: Jumlah pembayaran dengan auto-formatting Rupiah (required)
- **Documents**: Upload multiple file dokumen (PDF, JPG, PNG, max 2MB each, optional)
- **Vehicle**: Kendaraan terkait (auto-assigned berdasarkan context)

### 📋 Cara Menggunakan Certificate Receipt Module
1. **Akses Vehicles**: Klik menu "Vehicles" di sidebar
2. **Pilih Kendaraan**: Cari kendaraan yang akan dibuat tanda terima BPKB
3. **Akses Certificate Receipts**: Pada halaman vehicle detail, lihat section "Tanda Terima BPKB"
4. **Create Certificate Receipt**: Jika belum ada, klik "Buat Tanda Terima BPKB ->"
   - **Auto Certificate Number**: Nomor tanda terima otomatis dengan format 001/TT/BPKB/WOTO/XII/2025
   - **Single Receipt Rule**: Sistem mencegah pembuatan lebih dari satu tanda terima per kendaraan
   - **Comprehensive Form**: Isi semua field yang diperlukan (BPKB A/N, Faktur Asli A/N, dll)
   - **Real-time Validation**: Validasi otomatis untuk semua field yang required
5. **View Certificate Receipt**: Lihat tabel certificate receipt dengan informasi lengkap
6. **Edit Certificate Receipt**: Klik icon edit (pencil) untuk mengubah data
   - **Form Pre-population**: Data otomatis terisi di form edit
   - **Certificate Number Read-only**: Nomor tanda terima tidak dapat diubah (auto-generated)
7. **Delete Certificate Receipt**: Klik icon trash untuk menghapus dengan confirmation modal
8. **Print Certificate Receipt**: Klik icon printer untuk generate PDF
   - **Landscape PDF**: Template landscape dengan layout professional
   - **Dual Version**: ORIGINAL dan COPY dalam satu halaman A4
   - **Compact Design**: Layout yang efisien untuk menghemat kertas
   - **Company Branding**: Logo perusahaan dan informasi lengkap
9. **Certificate Receipt Audit Trail**: Klik menu "Certificate Receipts" untuk melihat audit trail
10. **Advanced Filtering**: Gunakan search, vehicle filter, dan pagination
11. **Export Data**: Gunakan tombol Excel/PDF untuk export data certificate receipt audit

### Form Fields Certificate Receipts
- **Certificate Number**: Auto-generated (format: 001/TT/BPKB/WOTO/XII/2025)
- **In The Name Of (BPKB A/N)**: Nama pada BPKB (required)
- **Original Invoice Name (Faktur Asli A/N)**: Nama pada faktur asli (required)
- **Photocopy ID Card Name (Fotocopy KTP A/N)**: Nama pada fotocopy KTP (required)
- **Receipt Form (Blanko Kwitansi)**: Informasi blanko kwitansi (required)
- **NIK**: Nomor Induk Kependudukan (required, max 16 characters)
- **Form A**: Informasi Form A (required)
- **Release of Title Letter (Surat Pelepasan Hak)**: Informasi surat pelepasan hak (required)
- **Others (Lain-lain)**: Informasi tambahan (optional, max 255 characters)
- **Receipt Date**: Tanggal tanda terima (required)
- **Transferee (Yang Menyerahkan)**: Nama yang menyerahkan dokumen (required)
- **Receiving Party (Yang Menerima)**: Nama yang menerima dokumen (required)
- **Vehicle**: Kendaraan terkait (auto-assigned berdasarkan context)

### Fitur Khusus Vendors Module
- **Vendor Management**: Database vendor/supplier kendaraan Indonesia
- **Contact Information**: Informasi lengkap vendor (name, contact, phone, email, address)
- **Vehicle Relationship**: Setiap vendor memiliki relasi dengan kendaraan
- **Audit Trail**: Tracking lengkap perubahan dengan before/after values
- **Export**: Export ke Excel/PDF dengan template yang konsisten
- **UI Consistency**: Interface mengikuti pola BrandAudit dengan card-based layout

### Cara Menggunakan Vendors Module
1. **Akses Vendors**: Klik menu "Vendors" di sidebar
2. **Add New Vendor**: Klik "Create Vendor" untuk menambah vendor baru
3. **Edit Vendor**: Klik icon edit untuk mengubah vendor
4. **View Details**: Klik icon eye untuk melihat detail vendor
5. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan
6. **Export Data**: Gunakan tombol Excel/PDF untuk export data

### Fitur Khusus Salesmen Module
- **Auto User Creation**: Otomatis membuat user account saat create salesman dengan role "salesman"
- **Default Password**: Password default "password" untuk semua salesman baru
- **Status Management**: Toggle Active/Inactive status untuk kontrol akses salesman
- **User Relationship**: Setiap salesman terhubung dengan user account untuk login
- **Audit Trail**: Tracking lengkap perubahan dengan before/after values
- **Export**: Export ke Excel/PDF dengan status information
- **UI Consistency**: Interface mengikuti pola module lain dengan status badges

### Cara Menggunakan Salesmen Module
1. **Akses Salesmen**: Klik menu "Salesmen" di sidebar
2. **Add New Salesman**: Klik "Create Salesman" untuk menambah salesman baru
   - Sistem otomatis create user account dengan role "salesman"
   - Password default akan diset ke "password"
3. **Edit Salesman**: Klik icon edit untuk mengubah data dan status salesman
   - **Status Control**: Toggle Active/Inactive untuk enable/disable akses
   - **Password Reset**: Sistem otomatis reset password ke "password" saat edit
4. **View Details**: Klik icon eye untuk melihat detail lengkap salesman
5. **Audit Trail**: Klik "Audit Trail" untuk melihat riwayat perubahan
6. **Export Data**: Gunakan tombol Excel/PDF untuk export data dengan status

## 🔐 Permissions & Roles

Sistem menggunakan Role-Based Access Control dengan permissions berikut:

### Permissions Detail
- `brand.*` - Manajemen brands (view, create, edit, delete)
- `vendor.*` - Manajemen vendors (view, create, edit, delete)
- `salesman.*` - Manajemen salesmen (view, create, edit, delete)
- `vehicle.*` - Manajemen vehicles (view, create, edit, delete)
- `vehicle-modal.view` - Akses card analisis harga jual di detail vehicle
- `vehicle-commission.*` - Manajemen commission records (view, create, edit, delete, audit)
- `vehicle-loan-calculation.*` - Manajemen loan calculation records (view, create, edit, delete, audit)
- `vehicle-purchase-payment.*` - Manajemen purchase payment records (view, create, edit, delete, audit)
- `vehicle-payment-receipt.*` - Manajemen payment receipt records (view, create, edit, delete, audit)
- `vehicle-registration-certificate-receipt.*` - Manajemen certificate receipt records (view, create, edit, delete, audit, print)
- `vehicle-handover.*` - Manajemen handover records (view, create, edit, delete, audit, print)
- `cost.*` - Manajemen cost records (view, create, edit, delete)
- `vehiclemodel.*` - Manajemen vehicle models (view, create, edit, delete)
- `category.*` - Manajemen categories (view, create, edit, delete)
- `type.*` - Manajemen types (view, create, edit, delete)
- `warehouse.*` - Manajemen warehouses (view, create, edit, delete)
- `user.*` - Manajemen users (view, create, edit, delete)
- `role.*` - Manajemen roles (view, create, edit, delete)
- `backup-restore.*` - Backup dan restore data

### Admin
- Akses penuh ke semua fitur
- Manajemen user dan role
- Manajemen vehicles, costs, brands, vendors, salesmen, models, categories, types, dan warehouses
- Analisis harga jual dan modal kendaraan (vehicle-modal.view)
- Backup & restore data
- Audit trail lengkap
- Approve/reject cost records

### Manager
- Melihat semua data
- CRUD lengkap kendaraan (vehicles) dan costs
- Analisis harga jual dan modal kendaraan (vehicle-modal.view)
- Manajemen brands, vendors, salesmen, models, categories, types, dan warehouses
- Approve/reject cost records dan transaksi
- Export laporan lengkap

### Staff
- CRUD kendaraan (vehicles) dengan approval manager
- CRUD cost records dengan approval manager
- Manajemen vendors, models, categories dan types kendaraan
- Pencatatan biaya kendaraan
- Generate laporan
- View audit trail

## 📊 Struktur Database

### Tabel Utama
- `users` - Data pengguna sistem
- `roles` & `permissions` - Sistem autorisasi
- `vehicles` - Data kendaraan lengkap dengan spesifikasi, status, dan data pembeli untuk kwitansi
- `vehicle_equipment` - Data kelengkapan peralatan kendaraan (STNK, kunci, ban serep, dll) dengan type sales/purchase
- `commissions` - Data komisi kendaraan (sales/purchase) dengan relasi ke vehicles
- `purchase_payments` - Data pembayaran pembelian kendaraan dengan multiple file upload dan auto-numbering
- `payment_receipts` - Data penerimaan pembayaran penjualan kendaraan dengan multiple file upload dan auto-numbering
- `certificate_receipts` - Data tanda terima BPKB kendaraan dengan auto-numbering dan comprehensive document tracking
- `loan_calculations` - Data perhitungan kredit kendaraan dengan relasi ke vehicles dan leasings
- `vehicle_handovers` - Data berita acara serah terima kendaraan dengan auto-numbering dan file upload support
- `costs` - Data biaya kendaraan dengan approval workflow
- `brands` - Merek mobil (31+ brand Indonesia)
- `vendors` - Vendor/supplier kendaraan (25+ vendor Indonesia)
- `salesmen` - Data salesman dengan auto-create user account (relasi ke users)
- `companies` - Data perusahaan untuk kwitansi (nama, alamat, telepon, email, logo, dll)
- `leasings` - Data perusahaan leasing/pembiayaan kendaraan
- `types` - Tipe kendaraan dengan format STNK (65+ tipe, relasi ke brands)
- `warehouses` - Lokasi penyimpanan kendaraan
- `vehicle_models` - Model kendaraan STNK (SEDAN, SUV, MPV, dll)
- `categories` - Kategori kendaraan STNK (MB, BB, BA, BK, dll)
- `activities` - Log aktivitas user (audit trail lengkap)

## 🔄 API Endpoints

Sistem menyediakan REST API untuk integrasi dengan aplikasi lain:

```
GET    /api/vehicles           - List semua kendaraan
GET    /api/vehicles/{id}      - Detail kendaraan tertentu
GET    /api/costs             - List semua cost records
GET    /api/costs/{id}        - Detail cost record tertentu
GET    /api/brands             - List semua brand
GET    /api/vendors            - List semua vendor
GET    /api/models             - List semua model kendaraan
GET    /api/categories         - List semua kategori kendaraan
GET    /api/types              - List semua tipe kendaraan
GET    /api/warehouses         - List warehouse
POST   /api/vehicles           - Tambah kendaraan baru
PUT    /api/vehicles/{id}      - Update kendaraan
DELETE /api/vehicles/{id}      - Hapus kendaraan
POST   /api/costs             - Tambah cost record baru
PUT    /api/costs/{id}        - Update cost record
DELETE /api/costs/{id}        - Hapus cost record
POST   /api/costs/{id}/approve - Approve cost record
POST   /api/costs/{id}/reject  - Reject cost record
POST   /api/vendors            - Tambah vendor baru
PUT    /api/vendors/{id}       - Update vendor
DELETE /api/vendors/{id}       - Hapus vendor
GET    /api/companies          - List data perusahaan untuk kwitansi
POST   /api/companies          - Tambah/update data perusahaan
PUT    /api/companies/{id}     - Update data perusahaan
GET    /api/salesmen           - List semua salesman
POST   /api/salesmen           - Tambah salesman baru (auto-create user)
PUT    /api/salesmen/{id}      - Update salesman dan status user
DELETE /api/salesmen/{id}      - Hapus salesman
POST   /api/vehicles/{id}/receipt - Generate kwitansi PDF untuk kendaraan tertentu
GET    /api/commissions        - List semua commission records
GET    /api/commissions/{id}   - Detail commission record tertentu
POST   /api/commissions        - Tambah commission record baru
PUT    /api/commissions/{id}   - Update commission record
DELETE /api/commissions/{id}  - Hapus commission record
GET    /api/loan-calculations - List semua loan calculation records
GET    /api/loan-calculations/{id} - Detail loan calculation record tertentu
POST   /api/loan-calculations  - Tambah loan calculation record baru
PUT    /api/loan-calculations/{id} - Update loan calculation record
DELETE /api/loan-calculations/{id} - Hapus loan calculation record
GET    /api/purchase-payments - List semua purchase payment records
GET    /api/purchase-payments/{id} - Detail purchase payment record tertentu
POST   /api/purchase-payments  - Tambah purchase payment record baru
PUT    /api/purchase-payments/{id} - Update purchase payment record
DELETE /api/purchase-payments/{id} - Hapus purchase payment record
GET    /api/payment-receipts - List semua payment receipt records
GET    /api/payment-receipts/{id} - Detail payment receipt record tertentu
POST   /api/payment-receipts  - Tambah payment receipt record baru
PUT    /api/payment-receipts/{id} - Update payment receipt record
DELETE /api/payment-receipts/{id} - Hapus payment receipt record
GET    /api/certificate-receipts - List semua certificate receipt records
GET    /api/certificate-receipts/{id} - Detail certificate receipt record tertentu
POST   /api/certificate-receipts  - Tambah certificate receipt record baru
PUT    /api/certificate-receipts/{id} - Update certificate receipt record
DELETE /api/certificate-receipts/{id} - Hapus certificate receipt record
GET    /api/vehicle-handovers - List semua vehicle handover records
GET    /api/vehicle-handovers/{id} - Detail vehicle handover record tertentu
POST   /api/vehicle-handovers  - Tambah vehicle handover record baru
PUT    /api/vehicle-handovers/{id} - Update vehicle handover record
DELETE /api/vehicle-handovers/{id} - Hapus vehicle handover record
GET    /api/leasings           - List semua data leasing
POST   /api/models             - Tambah model kendaraan baru
PUT    /api/models/{id}        - Update model kendaraan
DELETE /api/models/{id}        - Hapus model kendaraan
POST   /api/categories         - Tambah kategori kendaraan baru
PUT    /api/categories/{id}    - Update kategori kendaraan
DELETE /api/categories/{id}    - Hapus kategori kendaraan
POST   /api/types              - Tambah tipe kendaraan baru
PUT    /api/types/{id}         - Update tipe kendaraan
DELETE /api/types/{id}         - Hapus tipe kendaraan
```

## 🧪 Testing

Jalankan test suite:
```bash
php artisan test
```

## 📦 Deployment

### Production Setup
```bash
# Optimize aplikasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets untuk production
npm run build

# Setup queue worker (jika diperlukan)
php artisan queue:work
```

### Environment Variables
Pastikan setup environment variables berikut untuk production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_DATABASE=your_db_name

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host

# Storage
FILESYSTEM_DISK=public
```

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Changelog

### v1.17.0 - File Upload Management System
- ✅ **Complete Handover File Upload System**: Sistem upload berkas berita acara serah terima kendaraan
- ✅ **Handover Document Upload**: Upload multiple berkas handover dengan validasi lengkap (PDF, JPG, JPEG, PNG, max 2MB per file)
- ✅ **Handover File Management**: Storage management dengan auto-naming dan cleanup file lama
- ✅ **File Type Icons**: Display icon berdasarkan tipe file (PDF/document icons) untuk handover documents
- ✅ **Certificate Receipt File Upload System**: Sistem upload berkas tanda terima BPKB kendaraan
- ✅ **Certificate Receipt Document Upload**: Upload multiple berkas certificate receipt dengan validasi lengkap
- ✅ **Payment Completion Conditional Logic**: Section handover hanya muncul setelah pembayaran kendaraan lunas
- ✅ **File Validation & Security**: Comprehensive validation untuk file type, size, dan security
- ✅ **Audit Trail for File Operations**: Activity logging lengkap untuk semua upload/delete file operations
- ✅ **Real-time File Display**: Auto-refresh file display setelah upload/delete operations
- ✅ **Multiple File Support**: Support upload hingga 5 file per operation dengan comma-separated storage
- ✅ **File Cleanup Automation**: Automatic deletion of old files saat di-replace dengan file baru
- ✅ **Permission-based File Access**: vehicle-handover.* dan vehicle-registration-certificate-receipt.* permissions
- ✅ **Database Integration**: File paths stored as comma-separated strings dalam handover_file dan receipt_file columns
- ✅ **UI Enhancement**: Consistent file display dengan proper icons dan responsive layout

### v1.16.0 - Certificate Receipt Management System
- ✅ **Complete Certificate Receipt Module**: Sistem lengkap manajemen tanda terima BPKB kendaraan
- ✅ **Certificate Receipt CRUD Operations**: Create, Read, Update, Delete tanda terima BPKB dengan interface lengkap
- ✅ **Auto Certificate Number Generation**: Generate nomor tanda terima otomatis dengan format 001/TT/BPKB/WOTO/XII/2025
- ✅ **Single Receipt Validation**: Sistem mencegah pembuatan lebih dari satu tanda terima per kendaraan
- ✅ **Comprehensive Form Fields**: BPKB A/N, Faktur Asli A/N, Fotocopy KTP A/N, Blanko Kwitansi, NIK, Form A, Surat Pelepasan Hak, Lain-lain
- ✅ **Advanced Form Interface**: Modal form dengan validasi lengkap, error handling, dan resetValidation
- ✅ **Certificate Receipt Audit Trail**: Dedicated audit page dengan filtering, search, dan statistics dashboard
- ✅ **Advanced Audit Filtering**: Search by certificate number/in_the_name_of/user, vehicle filter, pagination
- ✅ **Audit Trail Statistics**: Real-time dashboard dengan total activities, today count, created/updated/deleted counters
- ✅ **Print Certificate Receipt**: Generate PDF tanda terima BPKB otomatis dalam format landscape
- ✅ **Compact Dual PDF Layout**: ORIGINAL dan COPY dalam satu halaman A4 landscape yang fit (8.5cm per section)
- ✅ **Professional PDF Template**: Template dengan logo perusahaan, data lengkap kendaraan, dan signature sections
- ✅ **Landscape PDF Optimization**: Layout landscape dengan two-column data arrangement untuk space efficiency
- ✅ **Permission-based Access**: vehicle-registration-certificate-receipt.* permissions untuk kontrol akses CRUD operations dan audit
- ✅ **Database Integration**: Foreign key ke vehicles table dengan relationship management
- ✅ **Print Count Tracking**: Track jumlah cetak tanda terima dengan timestamp untuk audit trail
- ✅ **Real-time Updates**: Auto-refresh data setelah create/update/delete operations dengan proper validation
- ✅ **UI Integration**: Seamless integration dengan vehicle detail page dan audit system
- ✅ **Error Handling**: Comprehensive validation dan user feedback untuk semua operations
- ✅ **Audit Trail**: Activity logging lengkap dengan before/after values untuk semua perubahan certificate receipts

### v1.15.0 - Payment Receipt Management System
- ✅ **Complete Payment Receipt Module**: Sistem lengkap manajemen penerimaan pembayaran penjualan kendaraan
- ✅ **Payment Receipt CRUD Operations**: Create, Read, Update, Delete penerimaan pembayaran dengan interface lengkap
- ✅ **Multiple File Upload**: Upload multiple dokumen penerimaan dengan auto-naming dan comma-separated storage
- ✅ **Auto Payment Number Generation**: Generate nomor penerimaan otomatis dengan format 001/PR/WOTO/I/2025
- ✅ **Selling Price Validation**: Prevent overpayment melebihi harga jual kendaraan dengan validation logic
- ✅ **Settlement Date Management**: Field tanggal harus diselesaikan ketika pembayaran belum lunas
- ✅ **Remaining Balance Calculation**: Otomatis hitung sisa pembayaran yang harus dilunasi
- ✅ **Advanced Form Interface**: Modal form dengan validasi lengkap, error handling, dan resetValidation
- ✅ **Payment Receipt Audit Trail**: Dedicated audit page dengan filtering, search, dan statistics dashboard
- ✅ **Advanced Audit Filtering**: Search by payment number/description/user, vehicle filter, pagination
- ✅ **Audit Trail Statistics**: Real-time dashboard dengan total activities, today count, created/updated/deleted counters
- ✅ **Permission-based Access**: vehicle-payment-receipt.* permissions untuk kontrol akses CRUD operations dan audit
- ✅ **Database Integration**: Foreign key ke vehicles table dengan document management dan file cleanup
- ✅ **Print Receipt Functionality**: Generate kwitansi PDF otomatis untuk penerimaan pembayaran terakhir
- ✅ **Terbilang Rupiah Helper**: Custom helper function untuk konversi angka ke teks bahasa Indonesia
- ✅ **Kwitansi PDF Template**: Template kwitansi dengan logo perusahaan, data lengkap, dan terbilang otomatis
- ✅ **Real-time Updates**: Auto-refresh data setelah create/update/delete operations dengan proper file handling
- ✅ **UI Integration**: Seamless integration dengan vehicle detail page dan audit system
- ✅ **File Management**: Proper file upload, storage, and deletion dengan multiple file support
- ✅ **Error Handling**: Comprehensive validation dan user feedback untuk semua operations
- ✅ **Print Count Tracking**: Track jumlah cetak kwitansi dengan timestamp untuk audit trail

### v1.14.0 - Purchase Payment Audit Trail System
- ✅ **Complete Purchase Payment Module**: Sistem lengkap manajemen pembayaran pembelian kendaraan
- ✅ **Purchase Payment CRUD Operations**: Create, Read, Update, Delete pembayaran pembelian dengan interface lengkap
- ✅ **Multiple File Upload**: Upload multiple dokumen pembayaran dengan auto-naming dan comma-separated storage
- ✅ **Auto Payment Number Generation**: Generate nomor pembayaran otomatis dengan format 0001/PP/WOTO/XII/2025
- ✅ **Purchase Price Validation**: Prevent overpayment melebihi harga beli kendaraan dengan validation logic
- ✅ **File Type Icons**: Display icon berdasarkan tipe file (PDF, JPG, PNG) dengan nama file lengkap
- ✅ **Advanced Form Interface**: Modal form dengan validasi lengkap, error handling, dan resetValidation
- ✅ **Purchase Payment Audit Trail**: Dedicated audit page dengan filtering, search, dan statistics dashboard
- ✅ **Advanced Audit Filtering**: Search by payment number/description/user/vehicle, vehicle filter, pagination
- ✅ **Audit Trail Statistics**: Real-time dashboard dengan total activities, today count, created/updated/deleted counters
- ✅ **Permission-based Access**: vehicle-purchase-payment.* permissions untuk kontrol akses CRUD operations dan audit
- ✅ **Database Integration**: Foreign key ke vehicles table dengan document management dan file cleanup
- ✅ **Real-time Updates**: Auto-refresh data setelah create/update/delete operations dengan proper file handling
- ✅ **UI Integration**: Seamless integration dengan vehicle detail page dan audit system
- ✅ **File Management**: Proper file upload, storage, and deletion dengan multiple file support
- ✅ **Error Handling**: Comprehensive validation dan user feedback untuk semua operations
- ✅ **Leasing Management**: Database leasings untuk menyimpan data perusahaan leasing/pembiayaan

### v1.13.0 - Loan Calculation Management System
- ✅ **Complete Loan Calculation Module**: Sistem lengkap manajemen perhitungan kredit kendaraan
- ✅ **Loan Calculation CRUD Operations**: Create, Read, Update, Delete perhitungan kredit dengan interface lengkap
- ✅ **Leasing Integration**: Relasi dengan tabel leasings untuk data perusahaan pembiayaan
- ✅ **Advanced Form Interface**: Modal form dengan validasi lengkap dan error handling
- ✅ **Activity Logging**: Activity logging lengkap menggunakan Spatie Activity Log dengan HasActivity trait
- ✅ **Loan Calculation Audit Trail**: Dedicated audit page dengan filtering, search, dan statistics dashboard
- ✅ **Advanced Audit Filtering**: Search by description/user/leasing, vehicle filter, pagination dengan 10-100 items per page
- ✅ **Audit Trail Statistics**: Real-time dashboard dengan total activities, today count, created/updated/deleted counters
- ✅ **Sorting by Leasing Name**: Data diurutkan berdasarkan nama leasing secara alfabetis untuk kemudahan pencarian
- ✅ **Permission-based Access**: vehicle-loan-calculation.* permissions untuk kontrol akses CRUD operations dan audit
- ✅ **Database Integration**: Foreign key ke vehicles dan leasings table dengan proper relationships
- ✅ **Real-time Updates**: Auto-refresh data setelah create/update/delete operations
- ✅ **UI Integration**: Seamless integration dengan vehicle detail page dan audit system
- ✅ **Audit Trail**: Activity logging lengkap dengan before/after values untuk semua perubahan
- ✅ **Model Relationships**: Proper Eloquent relationships antara Vehicle, LoanCalculation, dan Leasing
  
### v1.12.0 - Vehicle Completeness Checklist & Database Transactions
- ✅ **Vehicle Completeness Checklist System**: Sistem lengkap pencatatan kelengkapan peralatan kendaraan
- ✅ **Equipment Items Management**: 5 item kelengkapan (STNK Asli, Kunci Roda, Ban Serep, Kunci Serep, Dongkrak)
- ✅ **Visual Status Indicators**: Card dengan warna hijau (tersedia) dan merah (tidak tersedia) untuk setiap item
- ✅ **Auto-default STNK**: STNK Asli otomatis dicentang sebagai default saat create vehicle
- ✅ **Database Integration**: Data tersimpan di tabel vehicle_equipment dengan type purchase/sales
- ✅ **Equipment CRUD Operations**: Create, Read, Update, Delete equipment data di form vehicle
- ✅ **Equipment Display**: Section kelengkapan kendaraan di halaman vehicle detail dengan summary
- ✅ **Database Transaction Implementation**: Atomic operations untuk multi-table updates
- ✅ **Transaction Rollback**: Automatic rollback dengan file cleanup jika terjadi error
- ✅ **Error Handling**: Comprehensive error handling dengan logging dan user feedback
- ✅ **File Upload Safety**: File uploads dipindahkan sebelum transaction untuk safety
- ✅ **Equipment Relationship**: Proper Eloquent relationship antara Vehicle dan VehicleEquipment
- ✅ **Form Validation**: Equipment properties disimpan sebagai boolean dengan proper type casting
- ✅ **UI Consistency**: Interface mengikuti pola Flux UI dengan responsive grid layout

### v1.11.0 - Commission Management Module
- ✅ **Complete Commission Management System**: Sistem lengkap manajemen komisi kendaraan (sales & purchase)
- ✅ **Commission CRUD Operations**: Create, Read, Update, Delete komisi dengan interface lengkap
- ✅ **Commission Types**: Separate handling untuk Komisi Penjualan (Sales) dan Komisi Pembelian (Purchase)
- ✅ **Advanced Commission Forms**:
  - **Modal Form Interface**: Form create/edit dengan validasi lengkap dan error handling
  - **Auto-formatting Amount**: Format mata uang Rupiah otomatis dengan thousand separator
  - **Date Picker**: Input tanggal komisi dengan validasi dan format dd-mm-yyyy
  - **Commission Type Selection**: Dropdown untuk memilih tipe komisi
- ✅ **Commission Tables**: Separate visual tables untuk komisi penjualan (hijau) dan pembelian (biru)
- ✅ **Commission Audit Trail**: Dedicated audit page dengan filtering berdasarkan vehicle dan tipe komisi
- ✅ **Advanced Filtering**: Search, vehicle filter, commission type filter dengan pagination
- ✅ **Modal Confirmation Dialogs**: Confirmation modals untuk delete operations dengan detail komisi
- ✅ **Commission Statistics**: Dashboard statistics untuk total activities, today, created, updated, deleted
- ✅ **Real-time Updates**: Auto-refresh commission data setelah create/update/delete operations
- ✅ **Permission-based Access**: vehicle-commission.* permissions untuk semua operations
- ✅ **Export Features**: Excel dan PDF dengan template yang konsisten
- ✅ **UI Integration**: Seamless integration dengan vehicle detail page dan audit system

### v1.10.0 - Dashboard Enhancement & UI Improvements
- ✅ **Modern Dashboard Cards**: 4 metric cards dengan design modern dan responsive
  - **Vehicles Sold This Month**: Card hijau dengan icon shopping cart untuk tracking penjualan quantity
  - **Total Sales This Month**: Card biru dengan icon currency dollar untuk revenue tracking
  - **Vehicles Ready for Sale**: Card ungu dengan icon truck + info kendaraan baru bulan ini
  - **Total Cost This Month**: Card orange dengan icon receipt percent untuk cost tracking
- ✅ **Advanced Card Features**:
  - **Horizontal Layout**: Icon di kanan, text sejajar di kiri untuk space efficiency
  - **Hover Animations**: Smooth shadow dan translate effects saat hover
  - **Compact Design**: Reduced padding dan spacing untuk lebih banyak informasi
  - **Color-coded Icons**: Setiap card memiliki background icon dengan warna berbeda
  - **Dark Mode Support**: Full compatibility dengan light/dark theme switching
- ✅ **Responsive Grid Layout**: 4 columns desktop → 2 columns tablet → 1 column mobile
- ✅ **Transition Optimization**: Fixed flickering issues dengan transition-shadow dan transition-transform
- ✅ **Real-time Metrics**: Live calculation dari database untuk semua dashboard metrics
- ✅ **Business Intelligence**: Comprehensive overview untuk operational decision making

### v1.10.0 - Dashboard Enhancement & UI Improvements
- ✅ **Modern Dashboard Cards**: 4 metric cards dengan design modern dan responsive
  - **Vehicles Sold This Month**: Card hijau dengan icon shopping cart untuk tracking penjualan quantity
  - **Total Sales This Month**: Card biru dengan icon currency dollar untuk revenue tracking
  - **Vehicles Ready for Sale**: Card ungu dengan icon truck + info kendaraan baru bulan ini
  - **Total Cost This Month**: Card orange dengan icon receipt percent untuk cost tracking
- ✅ **Advanced Card Features**:
  - **Horizontal Layout**: Icon di kanan, text sejajar di kiri untuk space efficiency
  - **Hover Animations**: Smooth shadow dan translate effects saat hover
  - **Compact Design**: Reduced padding dan spacing untuk lebih banyak informasi
  - **Color-coded Icons**: Setiap card memiliki background icon dengan warna berbeda
  - **Dark Mode Support**: Full compatibility dengan light/dark theme switching
- ✅ **Responsive Grid Layout**: 4 columns desktop → 2 columns tablet → 1 column mobile
- ✅ **Transition Optimization**: Fixed flickering issues dengan transition-shadow dan transition-transform
- ✅ **Real-time Metrics**: Live calculation dari database untuk semua dashboard metrics
- ✅ **Business Intelligence**: Comprehensive overview untuk operational decision making

### v1.9.0 - Receipt/Kwitansi Penjualan Module
- ✅ **Complete Receipt Generation System**: Sistem lengkap generate kwitansi penjualan kendaraan PDF
- ✅ **PDF Receipt Template**: Template A4 portrait dengan layout formal dan profesional
- ✅ **Buyer Information Modal**: Form input data pembeli (nama, telepon, alamat) sebelum cetak kwitansi
- ✅ **Auto Receipt Number Generation**: Format KW/YYYYMMDD/XXXXX dengan sequence per tahun
- ✅ **Indonesian Rupiah Converter**: Fungsi terbilang lengkap untuk mata uang Rupiah (satu juta lima ratus ribu rupiah)
- ✅ **Company Logo Integration**: Logo perusahaan dari database ditampilkan di header kwitansi
- ✅ **Dynamic Company Data**: Informasi perusahaan (nama, alamat, telepon, email, website) diambil dari tabel companies
- ✅ **Base64 Image Encoding**: Optimasi logo untuk kompatibilitas DomPDF dengan base64 encoding
- ✅ **Professional Receipt Layout**: Header dengan logo kiri, title tengah, informasi terstruktur
- ✅ **Complete Transaction Details**: Menampilkan data kendaraan, pembeli, salesman, dan detail transaksi
- ✅ **Buyer Data Storage**: Data pembeli disimpan ke database vehicles (buyer_name, buyer_phone, buyer_address)
- ✅ **Receipt Number Persistence**: Nomor kwitansi tersimpan dan tidak berubah jika dicetak ulang
- ✅ **PDF Download**: Kwitansi langsung didownload dengan nama file yang descriptive
- ✅ **Mobile Responsive**: Interface modal form responsive untuk semua device
- ✅ **Form Validation**: Validasi lengkap untuk data pembeli (required fields)
- ✅ **Audit Trail Integration**: Activity logging untuk perubahan data buyer dan receipt number

### v1.8.0 - Salesmen Management Module
- ✅ **Complete Salesmen CRUD Module**: Sistem lengkap manajemen salesman dengan auto-create user account
- ✅ **Auto User Creation**: Otomatis membuat user account dengan role "salesman" saat create salesman
- ✅ **Default Password Management**: Password default "password" dengan auto-reset saat edit
- ✅ **Status Management**: Toggle Active/Inactive status untuk kontrol akses salesman
- ✅ **User-Salesman Relationship**: Foreign key relationship antara salesman dan users table
- ✅ **Comprehensive CRUD Operations**: Create, Read, Update, Delete dengan permission-based access
- ✅ **Audit Trail Integration**: Activity logging lengkap dengan before/after values
- ✅ **Advanced Filtering**: Search functionality dengan pagination
- ✅ **Export Features**: Excel dan PDF dengan template konsisten dan status information
- ✅ **PDF Landscape Orientation**: Export PDF dalam format landscape untuk data yang lebih luas
- ✅ **UI Consistency**: Interface mengikuti pola module lain dengan status badges
- ✅ **Permission System**: salesman.view, salesman.create, salesman.edit, salesman.delete
- ✅ **Database Integration**: Foreign key ke users table dengan status management
- ✅ **Responsive Design**: Mobile-friendly interface dengan proper spacing
- ✅ **Module Architecture**: Full integration dengan existing permission dan audit systems

### v1.7.0 - Price Analysis & Cost Status Enhancement
- ✅ **Analisis Harga Jual Komprehensif**: Card "Rincian Modal Mobil" dengan perhitungan modal lengkap
  - **Perhitungan Modal**: Total modal = harga beli + biaya kendaraan (approved + pending costs)
  - **Validasi Harga Display**: Otomatis cek apakah display_price mencakup total modal
  - **Perbandingan Harga**: Bandingkan display_price vs selling_price (harga actual terjual)
  - **Margin Keuntungan**: Hitung margin keuntungan untuk display dan actual selling price
  - **Rekomendasi Pricing**: Saran harga minimum untuk mencapai breakeven point
  - **Gap Analysis**: Analisis selisih antara ekspektasi pricing vs realita penjualan
- ✅ **Cost Status Badges**: Visual indicator warna untuk status cost approval
  - **Green Badge**: Approved costs
  - **Yellow Badge**: Pending costs
  - **Red Badge**: Rejected costs
- ✅ **Cost Records Pagination**: Sistem paginasi 10 items per halaman pada card vehicle detail
- ✅ **Price Analysis Dashboard**: 3-kolom layout untuk modal, display price, dan selling price
- ✅ **Business Intelligence**: Insights untuk optimasi pricing strategy dan profit margin
- ✅ **Real-time Calculations**: Perhitungan otomatis berdasarkan data cost terkini

### v1.6.0 - Cost Management Module
- ✅ **Complete Cost Management Module**: Sistem lengkap manajemen biaya kendaraan (service, spare parts, maintenance, dll)
- ✅ **Advanced Form Features**:
  - **Vendor Dropdown**: Pilih vendor dari database lengkap dengan foreign key relationship
  - **Auto-formatting Price**: Format harga Rupiah otomatis (150.000) dengan thousand separator dan debounce
  - **Document Upload**: Upload invoice/kwitansi dengan validasi file (PDF/JPG/PNG, max 5MB)
  - **Smart Date Validation**: Cost date tidak boleh di masa depan
- ✅ **Approval Workflow System**: Status Pending/Approved/Rejected dengan conditional actions
- ✅ **Advanced Filtering**: Triple filter (Status + Vehicle + Vendor) dengan clear filters functionality
- ✅ **Comprehensive CRUD Operations**: Create, Read, Update, Delete dengan permission-based access
- ✅ **Audit Trail Integration**: Activity logging lengkap dengan before/after values
- ✅ **Export Features**: Excel dan PDF dengan template konsisten dan filter support
- ✅ **UI Consistency**: Interface mengikuti pola BrandAudit dengan card-based layout
- ✅ **Permission System**: cost.view, cost.create, cost.edit, cost.delete
- ✅ **Database Integration**: Foreign key ke vehicles dan vendors table dengan cost_date field
- ✅ **Responsive Design**: Mobile-friendly interface dengan proper spacing
- ✅ **Module Refactoring Complete**: Full migration from ServicePart to Cost architecture

### v1.5.0 - Vehicles Management Module with Advanced Features
- ✅ **Complete Vehicles CRUD Module**: Sistem lengkap Create, Read, Update, Delete kendaraan
- ✅ **Advanced Form Features**:
  - **Quill Rich Text Editor**: Editor deskripsi dengan toolbar lengkap (bold, italic, lists, links)
  - **Auto-formatting Fields**: Police number (BG 1821 MY), kilometer (15.000), prices (150.000.000), cylinder capacity (1.500)
  - **Smart Progress Indicator**: Visual progress 5 langkah pengisian form
  - **Form State Persistence**: Auto-save localStorage setiap 30 detik, restore saat kembali
  - **Keyboard Shortcuts**: Ctrl+S (save), Ctrl+R (reset), Escape (back)
  - **Conditional Validation**: Selling fields muncul otomatis saat status "Sold"
  - **Cascading Dropdowns**: Brand → Type → Model filtering
  - **File Upload STNK**: Upload dengan preview dan validasi
- ✅ **Comprehensive Specifications**: Police number, year, fuel type, kilometer, cylinder capacity, colors, dll
- ✅ **Status Management**: Available/Sold dengan conditional fields
- ✅ **Vehicle Audit Trail**: Activity logging lengkap dengan before/after values
- ✅ **Export Features**: Excel dan PDF dengan template konsisten
- ✅ **Responsive UI**: Interface modern dengan visual grouping dan icons
- ✅ **Permission System**: vehicle.view, vehicle.create, vehicle.edit, vehicle.delete
- ✅ **JavaScript Optimization**: Clean console, no debug logs, proper error handling

### v1.4.0 - Vendors Management Module & PDF Template Standardization
- ✅ **Module Vendors Lengkap**: CRUD vendor/supplier kendaraan Indonesia
- ✅ **Vendor Contact Information**: Data lengkap (name, contact, phone, email, address)
- ✅ **Vendor Audit Trail**: Activity logging untuk semua perubahan vendors
- ✅ **Export Standardization**: Template PDF konsisten di semua module (Brands, Vendors, Categories, Types, Vehicle Models, Warehouses)
- ✅ **PDF Template Cleanup**: Styling bersih, font Arial, layout konsisten, footer minimal
- ✅ **Excel Template Standardization**: Format Excel yang seragam di semua module
- ✅ **UI Consistency**: Interface vendors mengikuti pola BrandAudit dengan card-based layout
- ✅ **Permission System**: Permissions lengkap untuk vendor management (vendor.view, vendor.create, vendor.edit, vendor.delete)

### v1.3.0 - Vehicle Models Management Module
- ✅ **Module Vehicle Models Lengkap**: CRUD model kendaraan dengan format STNK Indonesia
- ✅ **Database Vehicle Models**: 25+ model kendaraan STNK (SEDAN, SUV, MPV, MINIBUS, TRUCK, dll)
- ✅ **Vehicle Model Audit Trail**: Activity logging untuk semua perubahan vehicle models
- ✅ **Vehicle-Model Integration**: Relasi model dengan vehicles yang proper
- ✅ **STNK Classification**: Model berdasarkan klasifikasi resmi STNK Indonesia
- ✅ **Export Vehicle Models**: Export data vehicle models ke Excel dan PDF
- ✅ **UI Consistency**: Interface vehicle models mengikuti pola BrandAudit
- ✅ **Permission System**: Permissions lengkap untuk vehicle model management

### v1.2.0 - Categories Management Module
- ✅ **Module Categories Lengkap**: CRUD kategori kendaraan dengan format STNK Indonesia
- ✅ **Database Categories**: 26+ kategori kendaraan STNK (MB, BB, BA, BK, TK, DS, KH, dll)
- ✅ **Category Audit Trail**: Activity logging untuk semua perubahan categories
- ✅ **Vehicle-Category Integration**: Relasi category dengan vehicles yang proper
- ✅ **STNK Classification**: Kategori berdasarkan klasifikasi resmi STNK Indonesia
- ✅ **Export Categories**: Export data categories ke Excel dan PDF
- ✅ **UI Consistency**: Interface categories mengikuti pola BrandAudit
- ✅ **Permission System**: Permissions lengkap untuk category management

### v1.1.0 - Types Management Module
- ✅ **Module Types Lengkap**: CRUD tipe kendaraan dengan brand relationship
- ✅ **Database Types**: 65+ tipe kendaraan Indonesia dengan format STNK
- ✅ **Type Audit Trail**: Activity logging untuk semua perubahan types
- ✅ **Brand-Type Integration**: Relasi brand dengan types yang proper
- ✅ **Advanced Filtering**: Filter types berdasarkan brand dengan clear filters
- ✅ **Export Types**: Export data types ke Excel dan PDF
- ✅ **UI Consistency**: Interface types mengikuti pola BrandAudit
- ✅ **Permission System**: Permissions lengkap untuk type management

### v1.0.0
- ✅ Sistem manajemen brand lengkap (31+ brand Indonesia)
- ✅ CRUD kendaraan dengan spesifikasi lengkap
- ✅ Manajemen warehouse dan lokasi
- ✅ Activity logging
- ✅ Export Excel & PDF
- ✅ Backup & restore otomatis
- ✅ Role-based permissions

## 📞 Support

Jika Anda mengalami masalah atau memiliki pertanyaan:

- **Email**: support@woto.com
- **Documentation**: [Wiki](https://github.com/your-username/woto/wiki)
- **Issues**: [GitHub Issues](https://github.com/your-username/woto/issues)

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

**WOTO** - Membuat penjualan mobil bekas menjadi lebih mudah dan efisien! 🚗💨
