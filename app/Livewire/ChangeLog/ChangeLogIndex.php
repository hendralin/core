<?php

namespace App\Livewire\ChangeLog;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Change Log - WOTO v1.22.0')]
class ChangeLogIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $perPage = 5; // Jumlah versi per halaman

    public function render()
    {
        $changeLogsData = $this->getChangeLogs();
        $currentPage = $this->getPage();
        $perPage = $this->perPage;

        $changeLogs = new LengthAwarePaginator(
            collect($changeLogsData)->forPage($currentPage, $perPage),
            count($changeLogsData),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.change-log.change-log-index', compact('changeLogs'));
    }

    private function getChangeLogs()
    {
        return [
            [
                'version' => 'v1.22.0',
                'title' => 'Advanced Sales Report & Universal Month/Year Filter System',
                'date' => 'Latest',
                'color' => 'emerald',
                'features' => [
                    '✅ Complete Sales Report Module: Sistem lengkap laporan penjualan kendaraan dengan profit analytics dashboard',
                    '✅ Advanced Sales Analytics Dashboard: 4 stat cards modern untuk overview penjualan lengkap (Total Penjualan, Total Kendaraan Terjual, Total Keuntungan, % Margin Keuntungan)',
                    '✅ Comprehensive Profit Analysis: Perhitungan keuntungan komprehensif dengan margin terhadap harga jual aktual',
                    '✅ Vehicle Sales Cards Layout: Format kartu 2 kolom dengan informasi lengkap kendaraan, modal, keuntungan, buyer, dan salesman',
                    '✅ Advanced Cost Integration: Modal = harga beli + biaya approved + komisi pembelian untuk perhitungan akurat',
                    '✅ Color-coded Profit Display: Visual indicator hijau untuk profit, merah untuk loss per kendaraan',
                    '✅ Date-based Filtering: Filter periode tanggal dengan real-time calculation untuk semua metrics',
                    '✅ Advanced Export Features: Excel dan PDF export dengan template lengkap dan statistik summary',
                    '✅ Excel Export Enhancement: Tabel terstruktur dengan kolom Modal, Keuntungan, dan statistik lengkap',
                    '✅ PDF Export Enhancement: Layout kartu dengan informasi lengkap per kendaraan dan summary statistics',
                    '✅ Margin Calculation Optimization: Margin profit dihitung terhadap harga jual aktual bukan modal',
                    '✅ Real-time Statistics: Dashboard metrics yang update secara real-time berdasarkan filter aktif',
                    '✅ Responsive Card Layout: Grid 4 columns desktop → 2 columns tablet → 1 column mobile',
                    '✅ Audit Trail Integration: Activity logging lengkap untuk semua operasi sales report',
                    '✅ Permission-based Access: sales-reports.view permission untuk kontrol akses laporan penjualan',
                    '✅ UI/UX Excellence: Interface modern dengan hover effects, smooth transitions, dan visual hierarchy yang jelas',
                    '✅ Bahasa Indonesia Support: Semua interface, pesan, dan labels menggunakan bahasa Indonesia yang konsisten',
                    '🗓️ Universal Month/Year Filter System: Filter bulan/tahun dengan input type="month" di semua modul laporan',
                    '✅ Cash Report Month/Year Filter: Filter bulan/tahun dengan auto-update date range dan visual cost type indicators',
                    '✅ Sales Report Month/Year Filter: Filter bulan/tahun dengan auto-update date range dan profit analytics integration',
                    '✅ Cash Inject Month/Year Filter: Filter bulan/tahun dengan auto-update date range untuk inject records',
                    '✅ Cash Disbursement Month/Year Filter: Filter bulan/tahun dengan auto-update date range dan status filtering',
                    '✅ Cost Management Month/Year Filter: Filter bulan/tahun dengan auto-update date range dan multi-filter support',
                    '✅ Native HTML5 Month Picker: Input type="month" dengan native browser picker untuk UX optimal',
                    '✅ Auto-update Date Range Logic: Otomatis set dateFrom dan dateTo berdasarkan bulan/tahun yang dipilih',
                    '✅ Universal Filter Implementation: Konsistensi filter bulan/tahun di semua 5 modul laporan utama',
                    '✅ Enhanced Clear Filters: Tombol clear filter yang mendukung semua filter aktif termasuk bulan/tahun',
                    '✅ Footer Filter Display: Informasi filter aktif termasuk bulan/tahun di footer tabel laporan',
                    '✅ Mobile-Responsive Month Picker: Native date picker yang bekerja optimal di mobile devices',
                    '✅ Accessibility Compliant: Screen reader support dan keyboard navigation untuk input month',
                    '✅ Consistent UI/UX Design: Styling seragam dengan dark mode support di semua modul',
                    '✅ Real-time Filter Updates: Pagination reset dan data refresh otomatis saat filter berubah',
                    '✅ Filter State Persistence: Filter bulan/tahun tersimpan selama session dan dapat di-clear anytime'
                ]
            ],
            [
                'version' => 'v1.21.0',
                'title' => 'Vehicle File Management System',
                'date' => '',
                'color' => 'teal',
                'features' => [
                    '✅ Complete Cash Report Module: Sistem lengkap pelaporan arus kas dengan analytics dashboard',
                    '✅ Cost Type Analytics Dashboard: 4 stat cards untuk Service Parts, Showroom, Other Cost, dan Cash In dengan icon dan warna yang berbeda',
                    '✅ Advanced Cash Flow Analysis: Debet (pengeluaran), Kredit (pemasukan), Balance (saldo berjalan) dengan perhitungan akurat',
                    '✅ Opening Balance Integration: Saldo awal sebelum periode pelaporan dengan perhitungan kumulatif',
                    '✅ Running Balance Calculation: Perhitungan saldo berjalan per transaksi dengan logika yang tepat',
                    '✅ Transaction Details Enhancement: Integrasi nomor polisi kendaraan (police_number) dan nama vendor dalam deskripsi',
                    '✅ Professional Report Layout: Opening balance row (hanya di halaman 1), color-coded balances (hijau/merah), responsive design',
                    '✅ Advanced Export Features: Excel dan PDF dengan template profesional, opening balance integration, dan detail transaksi lengkap',
                    '✅ Real-time Statistics Dashboard: 4 stat cards dengan metrics real-time (total amount, transaction count, active indicators)',
                    '✅ Pagination-aware Opening Balance: Opening balance konsisten di semua halaman dengan perhitungan running balance yang akurat',
                    '✅ Audit Trail Integration: Activity logging lengkap untuk semua operasi cash report',
                    '✅ Permission-based Access: cash-report.view permission untuk kontrol akses laporan kas',
                    '✅ UI/UX Excellence: Interface modern dengan hover effects, smooth transitions, dan visual hierarchy yang jelas',
                    '✅ Responsive Design: Grid layout 4 columns desktop → 2 columns tablet → 1 column mobile',
                    '✅ Database Optimization: Query optimization dengan proper indexing dan relationship loading',
                    '✅ Error Handling: Comprehensive error handling untuk semua edge cases dan validation',
                    '✅ Bahasa Indonesia Support: Semua label, pesan, dan interface menggunakan bahasa Indonesia yang konsisten'
                ]
            ],
            [
                'version' => 'v1.19.0',
                'title' => 'Cash Inject Management System',
                'date' => '',
                'color' => 'purple',
                'features' => [
                    '✅ Complete Cash Inject Module: Sistem lengkap manajemen inject kas perusahaan',
                    '✅ Cash Inject CRUD Operations: Create, Read, Update, Delete inject kas dengan interface lengkap',
                    '✅ Cost Type Management: Implementasi cost_type \'cash\' untuk inject kas (vehicle_id NULL, vendor_id NULL)',
                    '✅ Auto Approval Workflow: Sistem langsung set status "approved" tanpa approval process',
                    '✅ Advanced Form Interface: Modal form dengan validasi lengkap, error handling, dan auto-formatting',
                    '✅ Cash Inject Audit Trail: Dedicated audit page dengan filtering, search, dan statistics dashboard',
                    '✅ Advanced Audit Filtering: Search by description/user, date filtering, pagination dengan 10-100 items per page',
                    '✅ Audit Trail Statistics: Real-time dashboard dengan total activities, today count, created/updated/deleted counters',
                    '✅ Card-based Audit UI: Audit trail menggunakan card-based layout dengan hover effects dan modern styling',
                    '✅ Bahasa Indonesia Support: Semua label dan pesan menggunakan bahasa Indonesia yang konsisten',
                    '✅ Permission-based Access: cash-inject.* permissions untuk kontrol akses CRUD operations dan audit',
                    '✅ Database Integration: Foreign key constraints dengan vehicle_id NULL, vendor_id NULL untuk cash injects',
                    '✅ Export Features: Excel dan PDF dengan template yang konsisten dan filter support',
                    '✅ Real-time Updates: Auto-refresh data setelah create/update/delete operations dengan proper validation',
                    '✅ UI Integration: Seamless integration dengan sidebar navigation dan permission system',
                    '✅ Form Validation: Comprehensive validation dengan cost_type hardcoded, date validation, amount formatting',
                    '✅ Audit Trail: Activity logging lengkap dengan before/after values untuk semua perubahan cash injects',
                    '✅ Module Separation: Cost Index menampilkan data vehicle_id NOT NULL, Cash Inject menampilkan cost_type \'cash\''
                ]
            ],
            [
                'version' => 'v1.18.0',
                'title' => 'Cash Disbursement Management System',
                'date' => '',
                'color' => 'green',
                'features' => [
                    '✅ Complete Cash Disbursement Module: Sistem lengkap manajemen pengeluaran kas perusahaan',
                    '✅ Cash Disbursement CRUD Operations: Create, Read, Update, Delete pengeluaran kas dengan interface lengkap',
                    '✅ Cost Type Management: Implementasi cost_type \'other_cost\' untuk pengeluaran kas (vehicle_id NULL, vendor_id NULL)',
                    '✅ Advanced Form Interface: Modal form dengan validasi lengkap, error handling, dan auto-formatting',
                    '✅ Cost Separation: Pemisahan data cost kendaraan vs pengeluaran kas berdasarkan vehicle_id dan vendor_id',
                    '✅ Cash Disbursement Audit Trail: Dedicated audit page dengan filtering, search, dan statistics dashboard',
                    '✅ Advanced Audit Filtering: Search by description/user, date filtering, pagination dengan 10-100 items per page',
                    '✅ Audit Trail Statistics: Real-time dashboard dengan total activities, today count, created/updated/deleted counters',
                    '✅ Permission-based Access: cashdisbursement.* permissions untuk kontrol akses CRUD operations dan audit',
                    '✅ Database Integration: Foreign key constraints dengan vehicle_id NULL, vendor_id NULL untuk cash disbursements',
                    '✅ Export Features: Excel dan PDF dengan template yang konsisten dan filter support',
                    '✅ Real-time Updates: Auto-refresh data setelah create/update/delete operations dengan proper validation',
                    '✅ UI Integration: Seamless integration dengan sidebar navigation dan permission system',
                    '✅ Form Validation: Comprehensive validation dengan cost_type hardcoded, date validation, amount formatting',
                    '✅ Audit Trail: Activity logging lengkap dengan before/after values untuk semua perubahan cash disbursements',
                    '✅ Module Separation: Cost Index menampilkan data vehicle_id NOT NULL, Cash Disbursement menampilkan vehicle_id NULL'
                ]
            ],
            [
                'version' => 'v1.17.0',
                'title' => 'File Upload Management System',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '✅ Complete Handover File Upload System: Sistem upload berkas berita acara serah terima kendaraan dengan validasi lengkap',
                    '✅ Certificate Receipt File Upload System: Sistem upload berkas tanda terima BPKB dengan validasi lengkap',
                    '✅ Payment Completion Conditional Logic: Section handover hanya muncul setelah pembayaran kendaraan lunas',
                    '✅ File Validation & Security: Comprehensive validation untuk file type, size, dan security (PDF, JPG, JPEG, PNG, max 2MB)',
                    '✅ Audit Trail for File Operations: Activity logging lengkap untuk semua upload/delete file operations',
                    '✅ Real-time File Display: Auto-refresh file display dengan icon berdasarkan tipe file setelah upload/delete',
                    '✅ Multiple File Support: Support upload hingga 5 file per operation dengan comma-separated storage',
                    '✅ File Cleanup Automation: Automatic deletion of old files saat di-replace dengan file baru',
                    '✅ Permission-based File Access: vehicle-handover.* dan vehicle-registration-certificate-receipt.* permissions',
                    '✅ Database Integration: File paths stored as comma-separated strings dalam handover_file dan receipt_file columns',
                    '✅ UI Enhancement: Consistent file display dengan proper icons dan responsive layout'
                ]
            ],
            [
                'version' => 'v1.16.0',
                'title' => 'Certificate Receipt Management System',
                'date' => '',
                'color' => 'orange',
                'features' => [
                    '✅ Complete Certificate Receipt Module: Sistem lengkap manajemen tanda terima BPKB kendaraan',
                    '✅ Certificate Receipt CRUD Operations: Create, Read, Update, Delete tanda terima BPKB dengan interface lengkap',
                    '✅ Auto Certificate Number Generation: Generate nomor tanda terima otomatis dengan format 001/TT/BPKB/WOTO/XII/2025',
                    '✅ Single Receipt Validation: Sistem mencegah pembuatan lebih dari satu tanda terima per kendaraan',
                    '✅ Comprehensive Form Fields: BPKB A/N, Faktur Asli A/N, Fotocopy KTP A/N, Blanko Kwitansi, NIK, Form A, Surat Pelepasan Hak, Lain-lain',
                    '✅ Print Certificate Receipt: Generate PDF tanda terima BPKB otomatis dalam format landscape',
                    '✅ Compact Dual PDF Layout: ORIGINAL dan COPY dalam satu halaman A4 landscape yang fit (8.5cm per section)',
                    '✅ Professional PDF Template: Template dengan logo perusahaan, data lengkap kendaraan, dan signature sections',
                    '✅ Certificate Receipt Audit Trail: Dedicated audit page dengan filtering, search, dan statistics dashboard',
                    '✅ Permission-based Access: vehicle-registration-certificate-receipt.* permissions untuk kontrol akses',
                    '✅ Real-time Updates: Auto-refresh data setelah create/update/delete operations'
                ]
            ],
            [
                'version' => 'v1.15.0',
                'title' => 'Payment Receipt Management System',
                'date' => '',
                'color' => 'emerald',
                'features' => [
                    '✅ Complete Purchase Payment Module: Sistem lengkap manajemen pembayaran pembelian kendaraan',
                    '✅ Purchase Payment CRUD Operations: Create, Read, Update, Delete pembayaran pembelian dengan interface lengkap',
                    '✅ Multiple File Upload: Upload multiple dokumen pembayaran dengan auto-naming dan comma-separated storage',
                    '✅ Auto Payment Number Generation: Generate nomor pembayaran otomatis dengan format 0001/PP/WOTO/XII/2025',
                    '✅ Purchase Price Validation: Prevent overpayment melebihi harga beli kendaraan dengan validation logic',
                    '✅ File Type Icons: Display icon berdasarkan tipe file (PDF, JPG, PNG) dengan nama file lengkap',
                    '✅ Advanced Form Interface: Modal form dengan validasi lengkap, error handling, dan resetValidation',
                    '✅ Purchase Payment Audit Trail: Dedicated audit page dengan filtering, search, dan statistics dashboard',
                    '✅ Advanced Audit Filtering: Search by payment number/description/user/vehicle, vehicle filter, pagination',
                    '✅ Audit Trail Statistics: Real-time dashboard dengan total activities, today count, created/updated/deleted counters',
                    '✅ Permission-based Access: vehicle-purchase-payment.* permissions untuk kontrol akses CRUD operations dan audit',
                    '✅ Database Integration: Foreign key ke vehicles table dengan document management dan file cleanup',
                    '✅ Real-time Updates: Auto-refresh data setelah create/update/delete operations dengan proper file handling',
                    '✅ UI Integration: Seamless integration dengan vehicle detail page dan audit system',
                    '✅ File Management: Proper file upload, storage, and deletion dengan multiple file support',
                    '✅ Error Handling: Comprehensive validation dan user feedback untuk semua operations'
                ]
            ],
            [
                'version' => 'v1.13.0',
                'title' => 'Loan Calculation Management System',
                'date' => '',
                'color' => 'purple',
                'features' => [
                    '💰 Complete Loan Calculation Module: Sistem lengkap manajemen perhitungan kredit kendaraan',
                    '✅ Loan Calculation CRUD Operations: Create, Read, Update, Delete perhitungan kredit dengan interface lengkap',
                    '✅ Leasing Integration: Relasi dengan tabel leasings untuk data perusahaan pembiayaan',
                    '✅ Advanced Form Interface: Modal form dengan validasi lengkap dan error handling',
                    '✅ Activity Logging: Activity logging lengkap menggunakan Spatie Activity Log dengan HasActivity trait',
                    '✅ Loan Calculation Audit Trail: Dedicated audit page dengan filtering, search, dan statistics dashboard',
                    '✅ Advanced Audit Filtering: Search by description/user/leasing, vehicle filter, pagination dengan 10-100 items per page',
                    '✅ Audit Trail Statistics: Real-time dashboard dengan total activities, today count, created/updated/deleted counters',
                    '✅ Sorting by Leasing Name: Data diurutkan berdasarkan nama leasing secara alfabetis untuk kemudahan pencarian',
                    '✅ Permission-based Access: vehicle-loan-calculation.* permissions untuk kontrol akses CRUD operations dan audit',
                    '✅ Database Integration: Foreign key ke vehicles dan leasings table dengan proper relationships',
                    '✅ Real-time Updates: Auto-refresh data setelah create/update/delete operations',
                    '✅ UI Integration: Seamless integration dengan vehicle detail page dan audit system',
                    '✅ Audit Trail: Activity logging lengkap dengan before/after values untuk semua perubahan',
                    '✅ Model Relationships: Proper Eloquent relationships antara Vehicle, LoanCalculation, dan Leasing',
                    '✅ Leasing Management: Database leasings untuk menyimpan data perusahaan leasing/pembiayaan'
                ]
            ],
            [
                'version' => 'v1.12.0',
                'title' => 'Vehicle Completeness Checklist & Database Transactions',
                'date' => '',
                'color' => 'teal',
                'features' => [
                    '🛠️ Complete Vehicle Completeness Checklist System: Sistem lengkap pencatatan kelengkapan peralatan kendaraan dengan visual status indicators',
                    '✅ Equipment Items Management: 5 item kelengkapan (STNK Asli, Kunci Roda, Ban Serep, Kunci Serep, Dongkrak) dengan auto-default STNK',
                    '✅ Visual Status Indicators: Card dengan warna hijau (tersedia) dan merah (tidak tersedia) untuk setiap item equipment',
                    '✅ Database Integration: Data tersimpan di tabel vehicle_equipment dengan type purchase/sales dan proper relationships',
                    '✅ Equipment CRUD Operations: Create, Read, Update, Delete equipment data di form vehicle create/edit',
                    '✅ Equipment Display: Section kelengkapan kendaraan di halaman vehicle detail dengan summary dan status count',
                    '✅ Database Transaction Implementation: Atomic operations untuk multi-table updates dengan error handling dan rollback',
                    '✅ Transaction Rollback: Automatic rollback dengan file cleanup jika terjadi error pada database operations',
                    '✅ Error Handling: Comprehensive error handling dengan logging dan user feedback untuk failed transactions',
                    '✅ File Upload Safety: File uploads dipindahkan sebelum transaction untuk safety dan consistency',
                    '✅ Equipment Relationship: Proper Eloquent relationship antara Vehicle dan VehicleEquipment models',
                    '✅ Form Validation: Equipment properties disimpan sebagai boolean dengan proper type casting ke database',
                    '✅ UI Consistency: Interface mengikuti pola Flux UI dengan responsive grid layout dan proper styling'
                ]
            ],
            [
                'version' => 'v1.11.0',
                'title' => 'Commission Management Module',
                'date' => '',
                'color' => 'purple',
                'features' => [
                    '💎 Complete Commission Management System: Sistem lengkap komisi kendaraan (sales & purchase) dengan interface modern',
                    '✅ Commission CRUD Operations: Create, Read, Update, Delete komisi dengan modal forms dan validasi lengkap',
                    '✅ Commission Types: Separate handling untuk Komisi Penjualan (hijau) dan Komisi Pembelian (biru)',
                    '✅ Advanced Commission Forms: Modal interface dengan auto-formatting amount, date picker, dan type selection',
                    '✅ Commission Tables: Visual tables terpisah dengan color coding dan totals untuk setiap jenis komisi',
                    '✅ Commission Audit Trail: Dedicated audit page dengan filtering berdasarkan vehicle dan tipe komisi',
                    '✅ Advanced Filtering: Search, vehicle filter, commission type filter dengan pagination dan real-time updates',
                    '✅ Modal Confirmation Dialogs: Confirmation modals untuk delete operations dengan detail komisi yang akan dihapus',
                    '✅ Permission-based Access: vehicle-commission.* permissions untuk semua operations (create, edit, delete, audit)',
                    '✅ Export Features: Excel dan PDF dengan template konsisten dan filtering support',
                    '✅ Real-time Updates: Auto-refresh commission data setelah create/update/delete operations'
                ]
            ],
            [
                'version' => 'v1.10.0',
                'title' => 'Dashboard Enhancement & UI Improvements',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '📊 Modern Dashboard Cards: 4 metric cards dengan design modern dan responsive (Vehicles Sold, Total Sales, Ready for Sale, Total Cost)',
                    '✅ Advanced Card Features: Horizontal layout dengan icon di kanan, hover animations, compact design, color-coded icons',
                    '✅ Responsive Grid Layout: 4 columns desktop → 2 columns tablet → 1 column mobile dengan proper spacing',
                    '✅ Transition Optimization: Fixed flickering issues dengan transition-shadow dan transition-transform',
                    '✅ Real-time Business Metrics: Live calculation dari database untuk semua dashboard metrics',
                    '✅ Dark Mode Support: Full compatibility dengan light/dark theme switching',
                    '✅ Business Intelligence Overview: Comprehensive operational dashboard untuk decision making'
                ]
            ],
            [
                'version' => 'v1.9.0',
                'title' => 'Receipt/Kwitansi Penjualan Module',
                'date' => '',
                'color' => 'emerald',
                'features' => [
                    '🧾 Complete Receipt Generation System dengan generate kwitansi PDF otomatis untuk kendaraan terjual',
                    '✅ PDF Receipt Template A4 portrait dengan layout formal dan profesional',
                    '✅ Buyer Information Modal: Form input data pembeli (nama, telepon, alamat) sebelum cetak kwitansi',
                    '✅ Auto Receipt Number Generation: Format KW/YYYYMMDD/XXXXX dengan sequence per tahun',
                    '✅ Indonesian Rupiah Converter: Fungsi terbilang lengkap untuk mata uang Rupiah (satu juta lima ratus ribu rupiah)',
                    '✅ Company Logo Integration: Logo perusahaan dari database ditampilkan di header kwitansi',
                    '✅ Dynamic Company Data: Informasi perusahaan (nama, alamat, telepon, email, website) diambil dari tabel companies',
                    '✅ Base64 Image Encoding: Optimasi logo untuk kompatibilitas DomPDF dengan base64 encoding',
                    '✅ Professional Receipt Layout: Header dengan logo kiri, title tengah, informasi terstruktur',
                    '✅ Complete Transaction Details: Menampilkan data kendaraan, pembeli, salesman, dan detail transaksi',
                    '✅ Buyer Data Storage: Data pembeli disimpan ke database vehicles (buyer_name, buyer_phone, buyer_address)',
                    '✅ Receipt Number Persistence: Nomor kwitansi tersimpan dan tidak berubah jika dicetak ulang',
                    '✅ PDF Download: Kwitansi langsung didownload dengan nama file yang descriptive',
                    '✅ Mobile Responsive: Interface modal form responsive untuk semua device',
                    '✅ Form Validation: Validasi lengkap untuk data pembeli (required fields)',
                    '✅ Audit Trail Integration: Activity logging untuk perubahan data buyer dan receipt number'
                ]
            ],
            [
                'version' => 'v1.8.0',
                'title' => 'Salesmen Management Module',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '👥 Complete Salesmen CRUD module dengan auto-create user account',
                    '✅ Auto user creation dengan role "salesman" dan default password "password"',
                    '✅ Status management: Toggle Active/Inactive untuk kontrol akses salesman',
                    '✅ Comprehensive CRUD operations dengan permission-based access (salesman.view, salesman.create, salesman.edit, salesman.delete)',
                    '✅ Audit trail integration dengan before/after values',
                    '✅ Export features: Excel dan PDF dengan template konsisten dan status information',
                    '✅ PDF landscape orientation untuk data yang lebih luas',
                    '✅ UI consistency dengan status badges dan responsive design',
                    '✅ Database integration: Foreign key ke users table dengan status management'
                ]
            ],
            [
                'version' => 'v1.7.0',
                'title' => 'Price Analysis & Cost Status Enhancement',
                'date' => '',
                'color' => 'green',
                'features' => [
                    '📊 Analisis Harga Jual Komprehensif dengan card "Rincian Modal Mobil"',
                    '✅ Perhitungan Modal: Total modal = harga beli + biaya kendaraan (approved + pending costs)',
                    '✅ Validasi Harga Display: Cek apakah harga jual mencakup total modal',
                    '✅ Perbandingan Harga: Bandingkan display_price vs selling_price (harga actual terjual)',
                    '✅ Margin Keuntungan: Hitung margin keuntungan untuk display dan actual price',
                    '✅ Rekomendasi Pricing: Saran harga minimum untuk mencapai breakeven point',
                    '✅ Status Badge: Visual indicator untuk cost approval (Approved/Pending/Rejected)',
                    '✅ Paginasi: Sistem paginasi untuk cost records dengan 10 items per halaman',
                    '✅ Gap Analysis: Analisis selisih antara harga display vs harga actual terjual'
                ]
            ],
            [
                'version' => 'v1.6.0',
                'title' => 'Cost Management Module (Refactored from Service & Parts)',
                'date' => '',
                'color' => 'green',
                'features' => [
                    '💰 Complete Cost Management module dengan sistem lengkap biaya kendaraan (service, spare parts, maintenance)',
                    '✅ Advanced form features dengan vendor dropdown, auto-formatting price (150.000), dan document upload',
                    '✅ Approval workflow system: Pending/Approved/Rejected dengan conditional actions',
                    '✅ Comprehensive CRUD operations dengan permission-based access (cost.view, cost.create, cost.edit, cost.delete)',
                    '✅ Database migration: service_parts table → costs table dengan cost_date field',
                    '✅ Route refactoring: service-parts/* → costs/* dengan full permission updates',
                    '✅ Component refactoring: 5 Livewire components dengan namespace Cost dan interface konsisten',
                    '✅ Export system: CostExport dengan template Excel/PDF dan field mapping lengkap',
                    '✅ File storage: photos/service-documents → photos/costs directory untuk document management',
                    '✅ Activity logging: Updated untuk cost record operations dengan before/after tracking'
                ]
            ],
            [
                'version' => 'v1.5.0',
                'title' => 'Vehicles Management Module with Advanced Features',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '🚗 Complete Vehicles CRUD module dengan interface lengkap',
                    '✅ Quill Rich Text Editor untuk deskripsi kendaraan dengan toolbar lengkap',
                    '✅ Auto-formatting fields: Police number (BG 1821 MY), kilometer (15.000), prices (150.000.000)',
                    '✅ Smart Progress Indicator dengan 5 langkah pengisian form',
                    '✅ Form State Persistence - auto-save localStorage setiap 30 detik',
                    '✅ Keyboard Shortcuts: Ctrl+S (save), Ctrl+R (reset), Escape (back)',
                    '✅ Conditional Validation - selling fields muncul otomatis saat status "Sold"',
                    '✅ Cascading Dropdowns - Brand → Type → Model filtering otomatis',
                    '✅ File Upload STNK dengan preview dan validasi',
                    '✅ Comprehensive Audit Trail dengan before/after values',
                    '✅ Export Excel & PDF dengan template konsisten',
                    '✅ JavaScript Optimization - clean console, no debug logs, proper error handling'
                ]
            ],
            [
                'version' => 'v1.4.0',
                'title' => 'Vendors Management Module & PDF Template Standardization',
                'date' => '',
                'color' => 'green',
                'features' => [
                    '✅ Module Vendors lengkap dengan contact information (name, contact, phone, email, address)',
                    '✅ Audit trail untuk semua perubahan vendors',
                    '✅ Standardisasi template PDF konsisten di semua module (Brands, Vendors, Categories, Types, Vehicle Models, Warehouses)',
                    '✅ Template Excel yang seragam di semua module',
                    '✅ UI consistency dengan BrandAudit pattern'
                ]
            ],
            [
                'version' => 'v1.3.0',
                'title' => 'Vehicle Models Management Module',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '✅ Module Vehicle Models lengkap dengan CRUD dan STNK classification',
                    '✅ Database 25+ model kendaraan STNK Indonesia (SEDAN, SUV, MPV, MINIBUS, dll)',
                    '✅ Audit trail untuk semua perubahan vehicle models',
                    '✅ Vehicle-Model integration dan export Excel/PDF',
                    '✅ UI consistency dengan BrandAudit pattern'
                ]
            ],
            [
                'version' => 'v1.2.0',
                'title' => 'Categories Management Module',
                'date' => '',
                'color' => 'green',
                'features' => [
                    '✅ Module Categories lengkap dengan CRUD dan STNK classification',
                    '✅ Database 26+ kategori kendaraan STNK Indonesia (MB, BB, BA, BK, TK, DS, dll)',
                    '✅ Audit trail untuk semua perubahan categories',
                    '✅ Vehicle-Category integration dan export Excel/PDF',
                    '✅ UI consistency dengan BrandAudit pattern'
                ]
            ],
            [
                'version' => 'v1.1.0',
                'title' => 'Types Management Module',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '✅ Module Types lengkap dengan CRUD dan brand relationship',
                    '✅ Database 65+ tipe kendaraan dengan format STNK Indonesia',
                    '✅ Audit trail untuk semua perubahan types',
                    '✅ Advanced filtering dan export Excel/PDF',
                    '✅ UI consistency dengan BrandAudit pattern'
                ]
            ],
            [
                'version' => 'v1.0.0',
                'title' => 'Initial Release',
                'date' => '',
                'color' => 'blue',
                'features' => [
                    '✅ Sistem manajemen brand lengkap (31+ brand Indonesia)',
                    '✅ CRUD kendaraan dengan spesifikasi lengkap',
                    '✅ Manajemen warehouse dan lokasi penyimpanan',
                    '✅ Activity logging dan audit trail',
                    '✅ Export data ke Excel dan PDF',
                    '✅ Role-based permissions system'
                ]
            ]
        ];
    }
}
