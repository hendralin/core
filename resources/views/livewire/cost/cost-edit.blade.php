<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Pembukuan Modal') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Ubah informasi pembukuan modal kendaraan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Form Content -->
        <div class="xl:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <flux:button
                    variant="primary"
                    size="sm"
                    href="{{ route('costs.index') }}"
                    wire:navigate
                    icon="arrow-uturn-left"
                    tooltip="Kembali ke Pembukuan Modal"
                >
                    Back
                </flux:button>
            </div>

            <form wire:submit="submit" id="cost-form" class="mt-6 space-y-6" enctype="multipart/form-data">
                <!-- Cost Information Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.identification class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <flux:heading size="md">Informasi Dasar Biaya</flux:heading>
                            <flux:subheading size="sm">Data identitas dan waktu biaya</flux:subheading>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <flux:select wire:model.live="cost_type" label="Tipe Pembukuan Modal" class="w-full">
                                <flux:select.option value="">Pilih Tipe Pembukuan Modal</flux:select.option>
                                <flux:select.option value="service_parts">Service & Parts</flux:select.option>
                                <flux:select.option value="other_cost">Biaya Lainnya</flux:select.option>
                            </flux:select>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Apa tipe pembukuan modal yang ingin diubah?</p>
                        </div>

                        <div class="space-y-1">
                            <flux:input wire:model="cost_date" type="date" label="Tanggal Pembukuan Modal" class="w-full" />
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Kapan pembukuan modal dilakukan?</p>
                        </div>

                        <div class="space-y-1">
                            <flux:select wire:model="vehicle_id" label="Kendaraan" class="w-full">
                            <flux:select.option value="">Pilih Kendaraan</flux:select.option>
                                @foreach($vehicles as $vehicle)
                                    <flux:select.option value="{{ $vehicle->id }}">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                                ></path>
                                            </svg>
                                            {{ $vehicle->police_number }} - {{ $vehicle->brand->name ?? '' }} {{ $vehicle->type->name ?? '' }}
                                        </div>
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Pilih kendaraan untuk pembukuan modal ini</p>
                        </div>
                    </div>
                </div>

                <!-- Vendor & Description Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.building-storefront class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <div>
                            <flux:heading size="md">Informasi Vendor & Detail Biaya</flux:heading>
                            <flux:subheading size="sm">Data vendor (jika Service & Parts) dan detail biaya yang dikeluarkan</flux:subheading>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($cost_type !== 'other_cost')
                        <div class="space-y-1">
                            <flux:select wire:model="vendor_id" label="Vendor" class="w-full">
                            <flux:select.option value="">Select Vendor</flux:select.option>
                                @foreach($vendors as $vendor)
                                    <flux:select.option value="{{ $vendor->id }}">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M8 11h8l.64 5.12a2 2 0 01-1.96 2.38H9.32a2 2 0 01-1.96-2.38L8 11z"
                                                ></path>
                                            </svg>
                                            {{ $vendor->name }}
                                        </div>
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Vendor mana yang menyediakan jasa/barang?</p>
                        </div>
                        @endif

                        <div class="space-y-1">
                            <flux:input
                                wire:model="total_price"
                                mask:dynamic="$money($input)"
                                icon="currency-dollar"
                                label="Total Biaya (Rp)"
                                placeholder="180.000"
                                class="w-full"
                                helper="Masukkan jumlah biaya yang dikeluarkan"
                            />
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Total biaya yang dikeluarkan</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <flux:textarea
                            wire:model="description"
                            label="Deskripsi Biaya"
                            placeholder="Masukkan detail biaya yang dikeluarkan, jasa yang dilakukan, barang yang dibeli, dan catatan tambahan..."
                            rows="4"
                        />
                    </div>

                    <div class="mt-6">
                        <flux:checkbox wire:model="big_cash" label="Pembayaran melalui Kas Besar" :checked="$big_cash" />
                        <p class="text-xs text-slate-500 dark:text-zinc-400 ml-6">Apakah pembayaran melalui Kas Besar?</p>
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.document-text class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        <div>
                            <flux:heading size="md">Dokumentasi (Opsional)</flux:heading>
                            <flux:subheading size="sm">Upload dokumen pendukung biaya</flux:subheading>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Document Preview -->
                        <div class="shrink-0">
                            @if($document)
                                <!-- Preview for newly uploaded file -->
                                <div class="relative">
                                    @if(str_starts_with($document->getMimeType(), 'image/'))
                                        <img src="{{ $document->temporaryUrl() }}" alt="New Document Preview" class="w-24 h-24 object-contain rounded-lg border-2 border-blue-200 dark:border-blue-700">
                                    @else
                                        <div class="w-24 h-24 border-2 border-blue-200 dark:border-blue-700 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-blue-900/20">
                                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <button type="button" wire:click="removeDocument" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors" title="Remove document">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @elseif($existing_document)
                                <!-- Preview for existing file -->
                                <div class="relative">
                                    @if(strtolower(pathinfo($existing_document, PATHINFO_EXTENSION)) === 'pdf')
                                        <div class="w-24 h-24 border-2 border-green-200 dark:border-green-700 rounded-lg flex items-center justify-center bg-green-50 dark:bg-green-900/20">
                                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <img src="{{ asset('photos/costs/' . $existing_document) }}" alt="Existing Document Preview" class="w-24 h-24 object-contain rounded-lg border-2 border-green-200 dark:border-green-700">
                                    @endif
                                    <button type="button" wire:click="removeDocument" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors" title="Remove document">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <!-- No document placeholder -->
                                <div class="w-24 h-24 border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Input -->
                        <div class="flex-1">
                            <flux:input
                                type="file"
                                wire:model="document"
                                label="Upload Dokumen"
                                placeholder="Choose file..."
                                accept=".pdf,.jpg,.jpeg,.png"
                            />
                            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                                Format yang didukung: PDF, JPG, JPEG, PNG. Maksimal ukuran: 5MB
                            </p>
                            @error('document')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-zinc-700">
                    <flux:button variant="ghost" href="{{ route('costs.index') }}" wire:navigate>
                        <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="plus">
                        Simpan
                </flux:button>
                </div>
            </form>
        </div>

        <!-- Help Sidebar -->
        <div class="xl:col-span-1">
            <div class="sticky top-6 space-y-6">
                <!-- Tips Panel -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <flux:icon.light-bulb class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <flux:heading size="sm" class="text-blue-900 dark:text-blue-100">Tips Pengisian</flux:heading>
                    </div>
                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Tipe pembukuan modal dapat diubah, namun perhatikan bahwa vendor akan otomatis dihapus jika memilih "Biaya Lainnya"</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Pilih tanggal pembukuan modal dengan benar</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Pastikan kendaraan sudah terdaftar di sistem</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Vendor hanya wajib diisi jika tipe Pembukuan Modal adalah Service & Parts</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Harga akan otomatis diformat dengan pemisah ribuan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Dokumen baru akan mengganti dokumen lama</span>
                        </li>
                    </ul>
                </div>

                <!-- Required Fields Summary -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <flux:icon.exclamation-triangle class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        <flux:heading size="sm" class="text-amber-900 dark:text-amber-100">Field Wajib</flux:heading>
                    </div>
                    <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1">
                        <li>• Tipe Biaya</li>
                        <li>• Tanggal Biaya</li>
                        <li>• Kendaraan</li>
                        <li>• Vendor (jika Service & Parts)</li>
                        <li>• Total Biaya</li>
                        <li>• Deskripsi Biaya</li>
                    </ul>
                </div>

                <!-- Quick Stats -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <flux:icon.chart-bar class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <flux:heading size="sm" class="text-green-900 dark:text-green-100">Statistik</flux:heading>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-green-800 dark:text-green-200">Total Biaya:</span>
                            <span class="font-medium text-green-900 dark:text-green-100">{{ \App\Models\Cost::count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-800 dark:text-green-200">Bulan Ini:</span>
                            <span class="font-medium text-green-900 dark:text-green-100">{{ \App\Models\Cost::whereMonth('cost_date', now()->month)->count() }}</span>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @error('*')
        <div class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                        ></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">Silakan periksa formulir</p>
                        <p class="text-xs text-red-600 dark:text-red-400">Beberapa field memerlukan perhatian Anda</p>
                    </div>
                </div>
            </div>
        </div>
    @enderror
</div>
