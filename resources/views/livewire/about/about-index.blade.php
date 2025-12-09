<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('About WOTO v1.22.0') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Informasi sistem dan aplikasi') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
            <!-- Application Details -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.cube class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                    Tentang WOTO
                </flux:heading>

                <div class="space-y-4">
                    <div>
                        <flux:text class="text-justify leading-relaxed">
                            <strong>WOTO v1.22.0</strong> adalah sistem manajemen modern untuk showroom penjualan mobil bekas yang dibangun dengan teknologi Laravel 12 dan Livewire 3. Sistem ini menyediakan solusi terintegrasi untuk manajemen inventori kendaraan, pencatatan biaya dengan approval workflow, pengelolaan kas perusahaan, sistem komisi lengkap, perhitungan kredit dengan integrasi leasing, serta berbagai modul bisnis lainnya yang dilengkapi dengan dashboard real-time, audit trail lengkap, dan pelaporan profesional.
                        </flux:text>
                    </div>

                    <div>
                        <flux:text class="font-medium mb-2">Tujuan:</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                            Memudahkan dan memperbaiki proses penjualan mobil bekas melalui sistem manajemen modern yang transparan dan terintegrasi.
                        </flux:text>
                    </div>

                    <div>
                        <flux:text class="font-medium mb-2">Target Pengguna:</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                            Showroom mobil bekas, dealer kendaraan, perusahaan rental, dan bisnis otomotif yang membutuhkan sistem manajemen inventori kendaraan yang komprehensif.
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
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Auto-formatting + localStorage + Multiple File Upload + Auto-numbering + File Upload Management</flux:text>
                    </div>
                    <div>
                        <flux:text class="font-medium text-sm">Export Tools</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Laravel Excel + DomPDF + Template Consistency</flux:text>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- System Information -->
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
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">bengkel.oprek@gmail.com</flux:text>
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
        </div>
    </div>

    <!-- Features -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mt-6">
        <flux:heading size="lg" class="mb-6 flex items-center gap-2">
            <flux:icon.star class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
            Fitur Utama
        </flux:heading>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Dashboard & Analytics -->
            <div class="bg-linear-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.chart-bar class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    <flux:text class="font-semibold text-blue-800 dark:text-blue-200">Dashboard & Analytics</flux:text>
                </div>
                <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Dashboard real-time metrics</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Advanced sales & cash reports</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Profit analytics & margin calculation</flux:text>
                    </div>
                </div>
            </div>

            <!-- Vehicle Management -->
            <div class="bg-linear-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.truck class="h-5 w-5 text-green-600 dark:text-green-400" />
                    <flux:text class="font-semibold text-green-800 dark:text-green-200">Vehicle Management</flux:text>
                </div>
                <div class="space-y-2 text-sm text-green-700 dark:text-green-300">
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Complete CRUD operations</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Vehicle completeness checklist</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Warehouse & brand management</flux:text>
                    </div>
                </div>
            </div>

            <!-- Financial Management -->
            <div class="bg-linear-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-lg p-4 border border-emerald-200 dark:border-emerald-700">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.currency-dollar class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    <flux:text class="font-semibold text-emerald-800 dark:text-emerald-200">Financial Management</flux:text>
                </div>
                <div class="space-y-2 text-sm text-emerald-700 dark:text-emerald-300">
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Cost management & approval workflow</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Cash disbursement & inject systems</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Commission & loan calculations</flux:text>
                    </div>
                </div>
            </div>

            <!-- Business Operations -->
            <div class="bg-linear-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.building-office class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                    <flux:text class="font-semibold text-purple-800 dark:text-purple-200">Business Operations</flux:text>
                </div>
                <div class="space-y-2 text-sm text-purple-700 dark:text-purple-300">
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Sales receipt generation</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Purchase payment management</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Certificate & handover management</flux:text>
                    </div>
                </div>
            </div>

            <!-- Data Management -->
            <div class="bg-linear-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg p-4 border border-orange-200 dark:border-orange-700">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.circle-stack class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                    <flux:text class="font-semibold text-orange-800 dark:text-orange-200">Data Management</flux:text>
                </div>
                <div class="space-y-2 text-sm text-orange-700 dark:text-orange-300">
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Brand, vendor & salesman databases</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Vehicle models & categories</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Company profile management</flux:text>
                    </div>
                </div>
            </div>

            <!-- System Features -->
            <div class="bg-linear-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-700">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.cog-6-tooth class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                    <flux:text class="font-semibold text-indigo-800 dark:text-indigo-200">System Features</flux:text>
                </div>
                <div class="space-y-2 text-sm text-indigo-700 dark:text-indigo-300">
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Complete audit trail system</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Advanced file upload management</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:icon.check-circle class="h-3 w-3 text-green-500" />
                        <flux:text>Auto backup & restore</flux:text>
                    </div>
                </div>
            </div>
            </div>

        <!-- Additional Info -->
        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <flux:text class="text-sm text-gray-600 dark:text-gray-400 text-center">
                <strong>25+ Fitur Lengkap</strong> dengan interface modern, auto-formatting, role-based permissions, dan export Excel/PDF untuk semua modul.
            </flux:text>
        </div>
    </div>
</div>
