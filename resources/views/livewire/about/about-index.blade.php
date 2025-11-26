<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('About WOTO') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Informasi sistem dan aplikasi') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- System Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.information-circle class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    Sistem Information
                </flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Application Name</flux:text>
                            <flux:text class="text-lg font-semibold">WOTO</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Version</flux:text>
                            <flux:text class="text-lg font-semibold">{{ $systemInfo['version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">PHP Version</flux:text>
                            <flux:text class="text-sm">{{ $systemInfo['php_version'] }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Laravel Version</flux:text>
                            <flux:text class="text-sm">{{ $systemInfo['laravel_version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Database</flux:text>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['database']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Environment</flux:text>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['environment']) }}</flux:text>
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Timezone</flux:text>
                        <flux:text class="text-sm">{{ $systemInfo['timezone'] }}</flux:text>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🚗 Total Vehicles</flux:text>
                            <flux:text class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ number_format($stats['vehicles_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">💰 Cost Records</flux:text>
                            <flux:text class="text-lg font-semibold text-cyan-600 dark:text-cyan-400">{{ number_format($stats['costs_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">👥 Total Users</flux:text>
                            <flux:text class="text-lg font-semibold text-green-600 dark:text-green-400">{{ number_format($stats['users_count']) }}</flux:text>
                        </div>
                    </div>

                    <!-- Quick Business Overview -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">📈 Vehicles Sold This Month</flux:text>
                            <flux:text class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['vehicles_sold_this_month']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🚚 Ready for Sale</flux:text>
                            <flux:text class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{ number_format($stats['vehicles_ready_for_sale']) }}</flux:text>
                        </div>
                    </div>


                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🏷️ Brands</flux:text>
                            <flux:text class="text-lg font-semibold text-orange-600 dark:text-orange-400">{{ number_format($stats['brands_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🏭 Vendors</flux:text>
                            <flux:text class="text-lg font-semibold text-pink-600 dark:text-pink-400">{{ number_format($stats['vendors_count']) }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-2">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">👥 Salesmen</flux:text>
                            <flux:text class="text-lg font-semibold text-teal-600 dark:text-teal-400">{{ number_format($stats['salesmen_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">📋 Models</flux:text>
                            <flux:text class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{ number_format($stats['vehicle_models_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🏷️ Categories</flux:text>
                            <flux:text class="text-lg font-semibold text-amber-600 dark:text-amber-400">{{ number_format($stats['categories_count']) }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-2">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🚗 Types</flux:text>
                            <flux:text class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['types_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🏢 Warehouses</flux:text>
                            <flux:text class="text-lg font-semibold text-slate-600 dark:text-slate-400">{{ number_format($stats['warehouses_count']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">🏭 Companies</flux:text>
                            <flux:text class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['companies_count'] ?? 0) }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.star class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                    Fitur Utama
                </flux:heading>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">📊 Dashboard Overview Real-time</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">4 metric cards modern: Vehicles Sold, Total Sales, Ready for Sale, Total Cost dengan hover animations dan responsive design</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">🚗 Manajemen Kendaraan Lengkap</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">CRUD vehicles dengan Quill editor, auto-formatting, progress indicator, dan state persistence</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">💰 Costs Management</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Sistem lengkap biaya kendaraan (service, spare parts, maintenance) dengan approval workflow, auto-formatting price (150.000), dan vendor integration</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Brand</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Database {{ $stats['brands_count'] }} merek mobil populer di Indonesia</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Vendor</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Database {{ $stats['vendors_count'] }} vendor/bengkel kendaraan Indonesia</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Salesmen</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Database {{ $stats['salesmen_count'] }} salesman dengan auto-create user account dan status management</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Models</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Database {{ $stats['vehicle_models_count'] }} model kendaraan STNK Indonesia (SEDAN, SUV, MPV, MINIBUS, dll)</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Categories</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Database {{ $stats['categories_count'] }} kategori kendaraan STNK Indonesia (MB, BB, BA, BK, TK, DS, dll)</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Types</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Database {{ $stats['types_count'] }} tipe kendaraan dengan format STNK Indonesia</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Manajemen Warehouse</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Lokasi penyimpanan kendaraan dengan tracking lengkap</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">🧾 Sistem Kwitansi Penjualan</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Generate kwitansi PDF otomatis dengan terbilang Rupiah, nomor otomatis KW/YYYYMMDD/XXXXX, logo perusahaan, dan data pembeli lengkap</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">🏢 Manajemen Perusahaan</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Data perusahaan dinamis untuk kwitansi (nama, alamat, telepon, email, logo) dengan base64 image encoding</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Audit Trail</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Tracking lengkap semua perubahan data dengan before/after</flux:text>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" />
                        <div>
                            <flux:text class="font-medium">Backup & Restore</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Sistem backup otomatis database dan file</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Details -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.cube class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                    Tentang WOTO
                </flux:heading>

                <div class="space-y-4">
                    <div>
                        <flux:text class="text-justify leading-relaxed">
                            <strong>WOTO v1.10.0</strong> adalah sistem manajemen lengkap untuk showroom penjualan mobil bekas yang dirancang khusus untuk membantu mengelola operasional bisnis dengan efisien. Sistem ini menyediakan solusi terintegrasi untuk manajemen inventori kendaraan, pencatatan biaya kendaraan (service, spare parts, maintenance) dengan approval workflow, audit trail lengkap, dan pelaporan bisnis dengan teknologi modern Laravel 12 dan Livewire 3, kini dilengkapi dengan dashboard overview real-time, advanced form features, auto-formatting prices, vendor integration, dan salesmen management dengan auto-create user account.
                        </flux:text>
                    </div>

                    <div>
                        <flux:text class="font-medium mb-2">Fitur Unggulan:</flux:text>
                        <div class="space-y-1">
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• 📊 Dashboard overview real-time dengan 4 metric cards modern (Vehicles Sold, Total Sales, Ready for Sale, Total Cost)</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• 🚗 Vehicles module lengkap dengan Quill editor, auto-formatting, progress indicator, dan form persistence</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• 💰 Costs module dengan approval workflow, auto-formatting price (150.000), dan vendor integration</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• Database {{ $stats['brands_count'] }} brand, {{ $stats['vendors_count'] }} vendor, {{ $stats['salesmen_count'] }} salesman, {{ $stats['vehicle_models_count'] }} model STNK, {{ $stats['categories_count'] }} kategori STNK, dan {{ $stats['types_count'] }} tipe kendaraan Indonesia</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• Audit trail lengkap dengan before/after tracking untuk semua module termasuk Vehicles & Costs</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• Export data ke Excel dan PDF dengan template konsisten di semua module</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• Advanced form features: auto-formatting prices, keyboard shortcuts, conditional validation, cascading dropdowns</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">• Role-based access control dengan permissions detail untuk semua module</flux:text>
                        </div>
                    </div>

                    <div>
                        <flux:text class="font-medium mb-2">Tujuan:</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                            Membuat proses penjualan mobil bekas menjadi lebih mudah, transparan, dan dapat dilacak dengan baik melalui sistem manajemen modern yang terintegrasi.
                        </flux:text>
                    </div>

                    <div>
                        <flux:text class="font-medium mb-2">Target Pengguna:</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                            Showroom mobil bekas, dealer kendaraan bermotor, perusahaan rental mobil, dan bisnis otomotif yang membutuhkan sistem manajemen inventori dan perbaikan kendaraan yang komprehensif.
                        </flux:text>
                    </div>
                </div>
            </div>

            <!-- Tech Stack -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.wrench-screwdriver class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                    Tech Stack
                </flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:text class="font-medium text-sm">Backend</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">{{ $systemInfo['laravel_version'] }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Frontend</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Livewire 3.x + Flux UI + Alpine.js</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Database</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">{{ ucfirst($systemInfo['database']) }} + Eloquent</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Rich Text Editor</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Quill.js untuk deskripsi</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Activity Logging</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Spatie Activity Log</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Permissions</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Spatie Permission</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Form Enhancement</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Auto-formatting + localStorage + File Management</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Export Tools</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Laravel Excel + DomPDF + Template Consistency</flux:text>
                    </div>
                </div>
            </div>

            <!-- Contact & Support -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.chat-bubble-left-right class="h-6 w-6 text-green-600 dark:text-green-400" />
                    Support & Contact
                </flux:heading>

                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <flux:icon.envelope class="h-5 w-5 text-gray-600 dark:text-zinc-400" />
                        <div>
                            <flux:text class="text-sm font-medium">Email Support</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">support@woto.com</flux:text>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <flux:icon.document-text class="h-5 w-5 text-gray-600 dark:text-zinc-400" />
                        <div>
                            <flux:text class="text-sm font-medium">Documentation</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Wiki Repository</flux:text>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <flux:icon.bug-ant class="h-5 w-5 text-gray-600 dark:text-zinc-400" />
                        <div>
                            <flux:text class="text-sm font-medium">Report Issues</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">GitHub Issues</flux:text>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Changelog -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.clock class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                    Version History
                </flux:heading>

                <div class="space-y-4">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-blue-600 dark:text-blue-400">v1.10.0 - Dashboard Enhancement & UI Improvements</flux:text>
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">Latest</span>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• 📊 Modern Dashboard Cards: 4 metric cards dengan design modern dan responsive (Vehicles Sold, Total Sales, Ready for Sale, Total Cost)</flux:text>
                            <flux:text>• ✅ Advanced Card Features: Horizontal layout dengan icon di kanan, hover animations, compact design, color-coded icons</flux:text>
                            <flux:text>• ✅ Responsive Grid Layout: 4 columns desktop → 2 columns tablet → 1 column mobile dengan proper spacing</flux:text>
                            <flux:text>• ✅ Transition Optimization: Fixed flickering issues dengan transition-shadow dan transition-transform</flux:text>
                            <flux:text>• ✅ Real-time Business Metrics: Live calculation dari database untuk semua dashboard metrics</flux:text>
                            <flux:text>• ✅ Dark Mode Support: Full compatibility dengan light/dark theme switching</flux:text>
                            <flux:text>• ✅ Business Intelligence Overview: Comprehensive operational dashboard untuk decision making</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-emerald-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-emerald-600 dark:text-emerald-400">v1.9.0 - Receipt/Kwitansi Penjualan Module</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• 🧾 Complete Receipt Generation System dengan generate kwitansi PDF otomatis untuk kendaraan terjual</flux:text>
                            <flux:text>• ✅ PDF Receipt Template A4 portrait dengan layout formal dan profesional</flux:text>
                            <flux:text>• ✅ Buyer Information Modal: Form input data pembeli (nama, telepon, alamat) sebelum cetak kwitansi</flux:text>
                            <flux:text>• ✅ Auto Receipt Number Generation: Format KW/YYYYMMDD/XXXXX dengan sequence per tahun</flux:text>
                            <flux:text>• ✅ Indonesian Rupiah Converter: Fungsi terbilang lengkap untuk mata uang Rupiah (satu juta lima ratus ribu rupiah)</flux:text>
                            <flux:text>• ✅ Company Logo Integration: Logo perusahaan dari database ditampilkan di header kwitansi</flux:text>
                            <flux:text>• ✅ Dynamic Company Data: Informasi perusahaan (nama, alamat, telepon, email, website) diambil dari tabel companies</flux:text>
                            <flux:text>• ✅ Base64 Image Encoding: Optimasi logo untuk kompatibilitas DomPDF dengan base64 encoding</flux:text>
                            <flux:text>• ✅ Professional Receipt Layout: Header dengan logo kiri, title tengah, informasi terstruktur</flux:text>
                            <flux:text>• ✅ Complete Transaction Details: Menampilkan data kendaraan, pembeli, salesman, dan detail transaksi</flux:text>
                            <flux:text>• ✅ Buyer Data Storage: Data pembeli disimpan ke database vehicles (buyer_name, buyer_phone, buyer_address)</flux:text>
                            <flux:text>• ✅ Receipt Number Persistence: Nomor kwitansi tersimpan dan tidak berubah jika dicetak ulang</flux:text>
                            <flux:text>• ✅ PDF Download: Kwitansi langsung didownload dengan nama file yang descriptive</flux:text>
                            <flux:text>• ✅ Mobile Responsive: Interface modal form responsive untuk semua device</flux:text>
                            <flux:text>• ✅ Form Validation: Validasi lengkap untuk data pembeli (required fields)</flux:text>
                            <flux:text>• ✅ Audit Trail Integration: Activity logging untuk perubahan data buyer dan receipt number</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-blue-600 dark:text-blue-400">v1.8.0 - Salesmen Management Module</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• 👥 Complete Salesmen CRUD module dengan auto-create user account</flux:text>
                            <flux:text>• ✅ Auto user creation dengan role "salesman" dan default password "password"</flux:text>
                            <flux:text>• ✅ Status management: Toggle Active/Inactive untuk kontrol akses salesman</flux:text>
                            <flux:text>• ✅ Comprehensive CRUD operations dengan permission-based access (salesman.view, salesman.create, salesman.edit, salesman.delete)</flux:text>
                            <flux:text>• ✅ Audit trail integration dengan before/after values</flux:text>
                            <flux:text>• ✅ Export features: Excel dan PDF dengan template konsisten dan status information</flux:text>
                            <flux:text>• ✅ PDF landscape orientation untuk data yang lebih luas</flux:text>
                            <flux:text>• ✅ UI consistency dengan status badges dan responsive design</flux:text>
                            <flux:text>• ✅ Database integration: Foreign key ke users table dengan status management</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-green-600 dark:text-green-400">v1.7.0 - Price Analysis & Cost Status Enhancement</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• 📊 Analisis Harga Jual Komprehensif dengan card "Rincian Modal Mobil"</flux:text>
                            <flux:text>• ✅ Perhitungan Modal: Total modal = harga beli + biaya kendaraan (approved + pending costs)</flux:text>
                            <flux:text>• ✅ Validasi Harga Display: Cek apakah harga jual mencakup total modal</flux:text>
                            <flux:text>• ✅ Perbandingan Harga: Bandingkan display_price vs selling_price (harga actual terjual)</flux:text>
                            <flux:text>• ✅ Margin Keuntungan: Hitung margin keuntungan untuk display dan actual price</flux:text>
                            <flux:text>• ✅ Rekomendasi Pricing: Saran harga minimum untuk mencapai breakeven point</flux:text>
                            <flux:text>• ✅ Status Badge: Visual indicator untuk cost approval (Approved/Pending/Rejected)</flux:text>
                            <flux:text>• ✅ Paginasi: Sistem paginasi untuk cost records dengan 10 items per halaman</flux:text>
                            <flux:text>• ✅ Gap Analysis: Analisis selisih antara harga display vs harga actual terjual</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-green-600 dark:text-green-400">v1.6.0 - Cost Management Module (Refactored from Service & Parts)</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• 💰 Complete Cost Management module dengan sistem lengkap biaya kendaraan (service, spare parts, maintenance)</flux:text>
                            <flux:text>• ✅ Advanced form features dengan vendor dropdown, auto-formatting price (150.000), dan document upload</flux:text>
                            <flux:text>• ✅ Approval workflow system: Pending/Approved/Rejected dengan conditional actions</flux:text>
                            <flux:text>• ✅ Comprehensive CRUD operations dengan permission-based access (cost.view, cost.create, cost.edit, cost.delete)</flux:text>
                            <flux:text>• ✅ Database migration: service_parts table → costs table dengan cost_date field</flux:text>
                            <flux:text>• ✅ Route refactoring: service-parts/* → costs/* dengan full permission updates</flux:text>
                            <flux:text>• ✅ Component refactoring: 5 Livewire components dengan namespace Cost dan interface konsisten</flux:text>
                            <flux:text>• ✅ Export system: CostExport dengan template Excel/PDF dan field mapping lengkap</flux:text>
                            <flux:text>• ✅ File storage: photos/service-documents → photos/costs directory untuk document management</flux:text>
                            <flux:text>• ✅ Activity logging: Updated untuk cost record operations dengan before/after tracking</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-blue-600 dark:text-blue-400">v1.5.0 - Vehicles Management Module with Advanced Features</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• 🚗 Complete Vehicles CRUD module dengan interface lengkap</flux:text>
                            <flux:text>• ✅ Quill Rich Text Editor untuk deskripsi kendaraan dengan toolbar lengkap</flux:text>
                            <flux:text>• ✅ Auto-formatting fields: Police number (BG 1821 MY), kilometer (15.000), prices (150.000.000)</flux:text>
                            <flux:text>• ✅ Smart Progress Indicator dengan 5 langkah pengisian form</flux:text>
                            <flux:text>• ✅ Form State Persistence - auto-save localStorage setiap 30 detik</flux:text>
                            <flux:text>• ✅ Keyboard Shortcuts: Ctrl+S (save), Ctrl+R (reset), Escape (back)</flux:text>
                            <flux:text>• ✅ Conditional Validation - selling fields muncul otomatis saat status "Sold"</flux:text>
                            <flux:text>• ✅ Cascading Dropdowns - Brand → Type → Model filtering otomatis</flux:text>
                            <flux:text>• ✅ File Upload STNK dengan preview dan validasi</flux:text>
                            <flux:text>• ✅ Comprehensive Audit Trail dengan before/after values</flux:text>
                            <flux:text>• ✅ Export Excel & PDF dengan template konsisten</flux:text>
                            <flux:text>• ✅ JavaScript Optimization - clean console, no debug logs, proper error handling</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-green-600 dark:text-green-400">v1.4.0 - Vendors Management Module & PDF Template Standardization</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• ✅ Module Vendors lengkap dengan contact information (name, contact, phone, email, address)</flux:text>
                            <flux:text>• ✅ Audit trail untuk semua perubahan vendors</flux:text>
                            <flux:text>• ✅ Standardisasi template PDF konsisten di semua module (Brands, Vendors, Categories, Types, Vehicle Models, Warehouses)</flux:text>
                            <flux:text>• ✅ Template Excel yang seragam di semua module</flux:text>
                            <flux:text>• ✅ UI consistency dengan BrandAudit pattern</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-blue-600 dark:text-blue-400">v1.3.0 - Vehicle Models Management Module</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• ✅ Module Vehicle Models lengkap dengan CRUD dan STNK classification</flux:text>
                            <flux:text>• ✅ Database 25+ model kendaraan STNK Indonesia (SEDAN, SUV, MPV, MINIBUS, dll)</flux:text>
                            <flux:text>• ✅ Audit trail untuk semua perubahan vehicle models</flux:text>
                            <flux:text>• ✅ Vehicle-Model integration dan export Excel/PDF</flux:text>
                            <flux:text>• ✅ UI consistency dengan BrandAudit pattern</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-green-600 dark:text-green-400">v1.2.0 - Categories Management Module</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• ✅ Module Categories lengkap dengan CRUD dan STNK classification</flux:text>
                            <flux:text>• ✅ Database 26+ kategori kendaraan STNK Indonesia (MB, BB, BA, BK, TK, DS, dll)</flux:text>
                            <flux:text>• ✅ Audit trail untuk semua perubahan categories</flux:text>
                            <flux:text>• ✅ Vehicle-Category integration dan export Excel/PDF</flux:text>
                            <flux:text>• ✅ UI consistency dengan BrandAudit pattern</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-blue-600 dark:text-blue-400">v1.1.0 - Types Management Module</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• ✅ Module Types lengkap dengan CRUD dan brand relationship</flux:text>
                            <flux:text>• ✅ Database 65+ tipe kendaraan dengan format STNK Indonesia</flux:text>
                            <flux:text>• ✅ Audit trail untuk semua perubahan types</flux:text>
                            <flux:text>• ✅ Advanced filtering dan export Excel/PDF</flux:text>
                            <flux:text>• ✅ UI consistency dengan BrandAudit pattern</flux:text>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:text class="font-semibold text-blue-600 dark:text-blue-400">v1.0.0 - Initial Release</flux:text>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                            <flux:text>• ✅ Sistem manajemen brand lengkap (31+ brand Indonesia)</flux:text>
                            <flux:text>• ✅ CRUD kendaraan dengan spesifikasi lengkap</flux:text>
                            <flux:text>• ✅ Manajemen warehouse dan lokasi penyimpanan</flux:text>
                            <flux:text>• ✅ Activity logging dan audit trail</flux:text>
                            <flux:text>• ✅ Export data ke Excel dan PDF</flux:text>
                            <flux:text>• ✅ Role-based permissions system</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
