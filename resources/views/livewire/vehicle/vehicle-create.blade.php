<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Vehicle') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new vehicle') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Progress Indicator -->
    <div class="mb-8 bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="sm">Progress Pengisian Form</flux:heading>
            <span class="text-sm text-gray-600 dark:text-zinc-400">
                Langkah {{ $current_step['step'] }} dari 5: {{ $current_step['name'] }}
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress_percentage }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-600 dark:text-zinc-400">
            <span class="{{ $current_step['step'] >= 1 ? 'text-blue-600 font-medium' : '' }}">Dasar</span>
            <span class="{{ $current_step['step'] >= 2 ? 'text-blue-600 font-medium' : '' }}">Detail</span>
            <span class="{{ $current_step['step'] >= 3 ? 'text-blue-600 font-medium' : '' }}">Teknis</span>
            <span class="{{ $current_step['step'] >= 4 ? 'text-blue-600 font-medium' : '' }}">Registrasi</span>
            <span class="{{ $current_step['step'] >= 5 ? 'text-blue-600 font-medium' : '' }}">Keuangan</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Form Content -->
        <div class="xl:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <flux:button variant="primary" size="sm" href="{{ route('vehicles.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Vehicles">Back</flux:button>

                <!-- Keyboard Shortcuts Info -->
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-zinc-400">
                    <flux:icon.command-line class="w-4 h-4" />
                    <span class="hidden sm:inline">Ctrl+S: Save • Ctrl+R: Reset • Esc: Back</span>
                </div>
            </div>

            <form wire:submit="submit" class="mt-6 space-y-6" enctype="multipart/form-data">

                <!-- Validation Errors Display -->
                @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <flux:icon.exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                        <flux:heading size="sm" class="text-red-900 dark:text-red-100">Perbaiki Kesalahan Berikut</flux:heading>
                    </div>
                    <ul class="text-sm text-red-800 dark:text-red-200 space-y-1">
                        @foreach($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <span class="text-red-600 mt-1">•</span>
                            <span>{{ $error }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Basic Information Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.identification class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <flux:heading size="md">Informasi Dasar</flux:heading>
                            <flux:subheading size="sm">Data identitas kendaraan</flux:subheading>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model.live.debounce.1500ms="police_number"
                                   label="Nomor Polisi *"
                                   icon="hashtag"
                                   placeholder="BG 1821 MY"
                                   helper="Format: XX 1234 ABC (contoh: BG 1821 MY)" />
                        <flux:select wire:model="year" label="Tahun *" icon="calendar">
                            <flux:select.option value="">{{ __('Pilih Tahun') }}</flux:select.option>
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 15;
                                $endYear = $currentYear + 1;
                            @endphp
                            @for($year = $endYear; $year >= $startYear; $year--)
                                <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                            @endfor
                        </flux:select>
                    </div>
                </div>

                <!-- Vehicle Details Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.squares-2x2 class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <div>
                            <flux:heading size="md">Detail Kendaraan</flux:heading>
                            <flux:subheading size="sm">Klasifikasi dan spesifikasi kendaraan</flux:subheading>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <flux:select wire:model.live="brand_id" label="Brand *" icon="tag">
                            <flux:select.option value="">{{ __('Pilih Brand') }}</flux:select.option>
                            @foreach($brands as $brand)
                                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model.live="type_id" label="Type *" icon="squares-2x2">
                            <flux:select.option value="">{{ __('Pilih Type') }}</flux:select.option>
                            @if($brand_id)
                                @foreach($types as $type)
                                    <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                                @endforeach
                            @endif
                        </flux:select>

                        <flux:select wire:model="category_id" label="Category *" icon="rectangle-stack">
                            <flux:select.option value="">{{ __('Pilih Category') }}</flux:select.option>
                            @foreach($categories as $category)
                                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="vehicle_model_id" label="Model *" icon="cube">
                            <flux:select.option value="">{{ __('Pilih Model') }}</flux:select.option>
                            @foreach($models as $model)
                                <flux:select.option value="{{ $model->id }}">{{ $model->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <!-- Technical Specifications Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.wrench-screwdriver class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                        <div>
                            <flux:heading size="md">Spesifikasi Teknis</flux:heading>
                            <flux:subheading size="sm">Informasi teknis dan spesifikasi kendaraan</flux:subheading>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:input wire:model="chassis_number" label="Nomor Rangka *" icon="hashtag" placeholder="Masukkan nomor rangka" />
                        <flux:input wire:model="engine_number" label="Nomor Mesin *" icon="cog" placeholder="Masukkan nomor mesin" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:input wire:model.live.debounce.1500ms="cylinder_capacity" label="Kapasitas Silinder (cc)" icon="beaker" placeholder="1.500" />
                        <flux:input wire:model="color" label="Warna" icon="swatch" placeholder="Putih" />
                        <flux:select wire:model="fuel_type" label="Tipe Bahan Bakar" icon="bolt">
                            <flux:select.option value="">{{ __('Pilih Tipe Bahan Bakar') }}</flux:select.option>
                            <flux:select.option value="Bensin">Bensin</flux:select.option>
                            <flux:select.option value="Solar">Solar</flux:select.option>
                        </flux:select>
                    </div>
                </div>

                <!-- Registration & Documents Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.document-text class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        <div>
                            <flux:heading size="md">Registrasi & Dokumen</flux:heading>
                            <flux:subheading size="sm">Informasi registrasi kendaraan dan dokumen terkait</flux:subheading>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:input wire:model.live.debounce.1500ms="kilometer" label="Kilometer *" icon="ticket" placeholder="15.000" />
                        <flux:select wire:model="warehouse_id" label="Warehouse *" icon="building-storefront">
                            <flux:select.option value="">{{ __('Pilih Warehouse') }}</flux:select.option>
                            @foreach($warehouses as $warehouse)
                                <flux:select.option value="{{ $warehouse->id }}">{{ $warehouse->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:input wire:model="vehicle_registration_date" label="Tanggal Registrasi *" icon="calendar" type="date" />
                        <flux:input wire:model="vehicle_registration_expiry_date" label="Tanggal Kadaluarsa *" icon="clock" type="date" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label>File STNK *</flux:label>
                            <flux:input type="file" wire:model="file_stnk"
                                      accept=".pdf,.jpg,.jpeg,.png"
                                      icon="document"
                                      helper="Upload file STNK (PDF, JPG, JPEG, PNG - maksimal 2MB)" />
                            @if($file_stnk)
                                <div class="text-sm text-green-600 flex items-center gap-1">
                                    <flux:icon.check-circle class="w-4 h-4" />
                                    File dipilih: {{ $file_stnk->getClientOriginalName() }}
                                </div>
                            @endif
                            <flux:error name="file_stnk" />
                        </div>
                    </div>
                </div>

                <!-- Vehicle Images Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.photo class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        <div>
                            <flux:heading size="md">Foto Kendaraan</flux:heading>
                            <flux:subheading size="sm">Upload foto kendaraan untuk dokumentasi</flux:subheading>
                        </div>
                    </div>

                    <!-- Image Upload Area -->
                    <div class="mb-6">
                        <div class="border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-lg p-8 text-center transition-colors hover:border-indigo-400 dark:hover:border-indigo-500"
                             wire:loading.class="opacity-50 cursor-not-allowed"
                             x-data="{
                                 dragover: false,
                                 isDragging: false,
                                 handleDrop(event) {
                                     const files = Array.from(event.dataTransfer.files);
                                     const validFiles = files.filter(file =>
                                         file.type.startsWith('image/') &&
                                         ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'].includes(file.type) &&
                                         file.size <= 5 * 1024 * 1024
                                     );

                                     if (validFiles.length > 0) {
                                         // Create a DataTransfer to simulate file input
                                         const dt = new DataTransfer();
                                         validFiles.forEach(file => dt.items.add(file));

                                         // Set files to the hidden input (this will trigger wire:model)
                                         document.getElementById('images-upload').files = dt.files;
                                         document.getElementById('images-upload').dispatchEvent(new Event('change', { bubbles: true }));
                                     }

                                     if (validFiles.length !== files.length) {
                                         alert('Beberapa file dilewati karena format tidak didukung atau ukuran terlalu besar.');
                                     }
                                 }
                             }"
                             @dragover.prevent="dragover = true; isDragging = true"
                             @dragleave.prevent="dragover = false; isDragging = false"
                             @drop.prevent="dragover = false; isDragging = false; handleDrop($event)"
                             :class="{ 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20': dragover || isDragging }">

                            <flux:icon.photo class="w-12 h-12 mx-auto text-gray-400 dark:text-zinc-500 mb-4" />
                            <div class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">
                                Upload Foto Kendaraan
                            </div>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">
                                Seret & lepaskan file gambar di sini, atau klik untuk memilih file. Anda dapat memilih beberapa gambar sekaligus atau menambahkannya satu per satu.
                            </p>
                            <flux:input type="file" wire:model="tempImages"
                                      multiple
                                      accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                      class="hidden"
                                      id="images-upload"
                                      x-ref="fileInput" />
                            <flux:button variant="outline" size="sm" onclick="document.getElementById('images-upload').click()">
                                <flux:icon.plus class="w-4 h-4 mr-2" />
                                Pilih File
                            </flux:button>
                            <p class="text-xs text-gray-500 dark:text-zinc-500 mt-2">
                                Format: JPG, PNG, GIF, WebP • Maksimal 5MB per gambar • Maksimal 10 gambar
                            </p>
                        </div>
                        <flux:error name="images.*" />
                    </div>

                    <!-- Image Previews -->
                    @if(!empty($images))
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($images as $index => $image)
                        <div class="relative group">
                            <div class="aspect-square bg-gray-100 dark:bg-zinc-700 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-600">
                                @if($image)
                                <img src="{{ $image->temporaryUrl() }}"
                                     alt="Preview {{ $index + 1 }}"
                                     class="w-full h-full object-cover">
                                @endif
                            </div>
                            <!-- Remove Button -->
                            <button type="button"
                                    wire:click="removeImage({{ $index }})"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors"
                                    title="Hapus gambar">
                                <flux:icon.x-mark class="w-4 h-4" />
                            </button>
                            <!-- File Info -->
                            <div class="mt-2 text-xs text-gray-600 dark:text-zinc-400 truncate">
                                {{ $image ? $image->getClientOriginalName() : '' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-zinc-500">
                                {{ $image ? number_format($image->getSize() / 1024, 1) . ' KB' : '' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Vehicle Completeness Checklist Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.check-circle class="w-5 h-5 text-teal-600 dark:text-teal-400" />
                        <div>
                            <flux:heading size="md">Kelengkapan Kendaraan</flux:heading>
                            <flux:subheading size="sm">Periksa kelengkapan kendaraan yang tersedia</flux:subheading>
                        </div>
                    </div>
                    <flux:fieldset>
                        <flux:legend>Kelengkapan Kendaraan</flux:legend>
                        <flux:description>Centang kelengkapan yang tersedia pada kendaraan ini</flux:description>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 *:gap-x-2">
                            <flux:checkbox wire:model="stnk_asli" label="STNK Asli" />
                            <flux:checkbox wire:model="kunci_roda" label="Kunci Roda" />
                            <flux:checkbox wire:model="ban_serep" label="Ban Serep" />
                            <flux:checkbox wire:model="kunci_serep" label="Kunci Serep" />
                            <flux:checkbox wire:model="dongkrak" label="Dongkrak" />
                        </div>
                    </flux:fieldset>
                </div>

                <!-- Financial Information Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.currency-dollar class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        <div>
                            <flux:heading size="md">Informasi Keuangan</flux:heading>
                            <flux:subheading size="sm">Data pembelian dan penjualan kendaraan</flux:subheading>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:input wire:model="purchase_date" label="Tanggal Pembelian *" icon="calendar" type="date" />
                        <div class="relative">
                            <flux:input wire:model.live.debounce.1500ms="purchase_price" icon="currency-dollar" label="Harga Beli *" placeholder="150.000.000" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:select wire:model.live="status"
                                    label="Status Kendaraan *"
                                    icon="check-circle"
                                    >
                            <flux:select.option value="1">🚗 Tersedia</flux:select.option>
                            {{-- <flux:select.option value="0">💰 Terjual</flux:select.option> --}}
                        </flux:select>
                        <div class="relative">
                            <flux:input wire:model.live.debounce.1500ms="roadside_allowance" icon="currency-dollar" label="Biaya Uang Jalan *" placeholder="250.000" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:input wire:model.live.debounce.1500ms="display_price" icon="currency-dollar" label="Harga Tunai *" placeholder="180.000.000" />
                        <flux:input wire:model.live.debounce.1500ms="loan_price" icon="currency-dollar" label="Harga Kredit *" placeholder="198.000.000" />
                    </div>

                    <!-- Selling Information (only shown when status is Sold) -->
                    @if($status == '0')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:input wire:model="selling_date" label="Tanggal Penjualan *" icon="calendar" type="date" />
                        <div class="relative">
                            <flux:input wire:model.live.debounce.1500ms="selling_price" icon="currency-dollar" label="Harga Penjualan *" placeholder="180.000.000" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <flux:select wire:model="salesman_id" label="Salesman *" icon="user">
                            <flux:select.option value="">{{ __('Pilih Salesman') }}</flux:select.option>
                            @foreach($salesmen as $salesman)
                                <flux:select.option value="{{ $salesman->id }}">{{ $salesman->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <div></div> <!-- Empty column for layout balance -->
                    </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6">
                        <div wire:ignore>
                            <label for="description-editor" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                                <i class="fas fa-file-alt mr-2"></i>Deskripsi
                            </label>
                            <div id="description-editor" style="height: 120px;"></div>
                        </div>
                        <input type="hidden" wire:model="description" id="description-hidden">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <flux:button variant="ghost" href="{{ route('vehicles.index') }}" wire:navigate>
                        <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                        Batal
                    </flux:button>

                    <div class="flex gap-3">
                        <flux:modal.trigger name="reset-confirmation">
                            <flux:button type="button" variant="outline" id="reset-form-btn">
                                Reset Form
                            </flux:button>
                        </flux:modal.trigger>
                        <flux:button type="submit" variant="primary" icon="plus" id="submit-form-btn">
                            Simpan
                        </flux:button>
                    </div>
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
                            <span>Nomor polisi harus mengikuti format: XX 1234 ABC</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Upload STNK yang jelas dan masih berlaku</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Harga akan otomatis diformat dengan pemisah ribuan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Harga Jual digunakan untuk harga tampilan di website/front-end</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-600 mt-1">•</span>
                            <span>Field penjualan hanya muncul jika status "Terjual"</span>
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
                        <li>• Nomor Polisi</li>
                        <li>• Tahun</li>
                        <li>• Brand, Type, Category, Model</li>
                        <li>• Nomor Rangka & Mesin</li>
                        <li>• Kilometer</li>
                        <li>• Warehouse</li>
                        <li>• Tanggal Registrasi & Kadaluarsa</li>
                        <li>• File STNK</li>
                        <li>• Tanggal & Harga Beli</li>
                        <li>• Harga Tunai & Harga Kredit</li>
                        <li>• Biaya Uang Jalan</li>
                        <li>• Status</li>
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
                            <span class="text-green-800 dark:text-green-200">Total Kendaraan:</span>
                            <span class="font-medium text-green-900 dark:text-green-100">{{ \App\Models\Vehicle::count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-800 dark:text-green-200">Tersedia:</span>
                            <span class="font-medium text-green-900 dark:text-green-100">{{ \App\Models\Vehicle::where('status', '1')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-800 dark:text-green-200">Terjual:</span>
                            <span class="font-medium text-green-900 dark:text-green-100">{{ \App\Models\Vehicle::where('status', '0')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <flux:modal name="reset-confirmation" wire:model="showResetModal" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Reset Form?</flux:heading>
                <flux:text class="mt-2">
                    <p>Anda akan mereset semua data yang telah diisi dalam form ini.</p>
                    <p class="text-amber-600 dark:text-amber-400 font-medium">Data yang belum disimpan akan hilang permanen.</p>
                    <p>Apakah Anda yakin ingin melanjutkan?</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="cancelReset">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="resetForm" variant="danger">
                    Reset Form
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- JavaScript -->
    <script>
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Only handle shortcuts when not in input/textarea/select elements
            const activeElement = document.activeElement;
            const isFormElement = activeElement.tagName === 'INPUT' ||
                                activeElement.tagName === 'TEXTAREA' ||
                                activeElement.tagName === 'SELECT';

            // Ctrl+S to save form (only when not in form elements)
            if (e.ctrlKey && e.key === 's' && !isFormElement) {
                e.preventDefault();
                e.stopPropagation();
                const submitBtn = document.getElementById('submit-form-btn');
                if (submitBtn) {
                    submitBtn.click();
                }
                return false;
            }

            // Ctrl+R to reset form (with confirmation)
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                e.stopPropagation();
                // Trigger the reset button which opens the modal
                const resetBtn = document.getElementById('reset-form-btn');
                if (resetBtn) {
                    resetBtn.click();
                }
                return false;
            }

            // Escape to go back
            if (e.key === 'Escape' && !isFormElement) {
                e.preventDefault();
                const backBtn = document.querySelector('a[href*="vehicles"]');
                if (backBtn) window.location.href = backBtn.href;
                return false;
            }
        });

        // Global Quill instance
        window.quillInstance = null;

        // Use window.initQuill to prevent duplicate declarations
        if (!window.initQuill) {
            window.initQuill = () => {
                const editorElement = document.getElementById('description-editor');

                // Check if Quill is already initialized
                if (window.quillInstance) {
                    return true;
                }

                // Check if toolbar already exists (in case instance was lost but DOM wasn't)
                const existingToolbar = editorElement?.previousElementSibling?.classList.contains('ql-toolbar') ||
                                       editorElement?.querySelector('.ql-toolbar') ||
                                       document.querySelector('.ql-toolbar');

                if (existingToolbar) {
                    existingToolbar.remove();
                }

                if (editorElement && typeof Quill !== 'undefined') {
                    try {
                        // Clear any existing content first
                        editorElement.innerHTML = '';

                        window.quillInstance = new Quill('#description-editor', {
                            theme: 'snow',
                            placeholder: 'Masukkan deskripsi kendaraan...',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline', 'strike'],
                                    ['blockquote', 'code-block'],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });

                    // Sync Quill content to hidden input
                    window.quillInstance.on('text-change', function() {
                        const html = window.quillInstance.root.innerHTML;
                        const hiddenInput = document.getElementById('description-hidden');
                        if (hiddenInput) {
                            hiddenInput.value = html;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });

                    // Load initial content if exists
                    const hiddenInput = document.getElementById('description-hidden');
                    if (hiddenInput && hiddenInput.value) {
                        window.quillInstance.root.innerHTML = hiddenInput.value;
                    }

                    return true;
                } catch (error) {
                    console.error('Error initializing Quill:', error);
                    return false;
                }
            } else {
                return false;
            }
            };
        }

        // Initialize after everything is loaded
        if (!window.tryInitQuill) {
            window.tryInitQuill = () => {
            const initFn = window.initQuill;
            if (initFn && initFn()) {
                // Success
            } else {
                // Retry after a short delay
                setTimeout(() => {
                    if (initFn && !initFn()) {
                        setTimeout(() => initFn && initFn(), 1000);
                    }
                }, 200);
            }
            };
        }

        // Multiple initialization attempts
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.tryInitQuill && window.tryInitQuill(), 100);
        });

        document.addEventListener('livewire:loaded', () => {
            setTimeout(() => window.tryInitQuill && window.tryInitQuill(), 50);
        });

        // Listen for Livewire navigate events (when using wire:navigate)
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => window.tryInitQuill && window.tryInitQuill(), 100);
        });

        // Cleanup Quill instance when navigating away
        document.addEventListener('livewire:navigating', () => {
            if (window.quillInstance) {
                window.quillInstance = null;
            }
        });

        // More aggressive re-initialization after Livewire updates
        document.addEventListener('livewire:updated', () => {
            // Wait a bit longer for DOM to settle, then force re-init
            setTimeout(() => {
                // Always try to re-initialize after Livewire updates
                const editorElement = document.getElementById('description-editor');
                if (editorElement && typeof Quill !== 'undefined') {

                    // Save current content if Quill exists
                    let currentContent = '';
                    if (quillInstance) {
                        currentContent = quillInstance.root.innerHTML;
                    }

                    // Clear any existing Quill toolbar
                    const toolbar = editorElement.querySelector('.ql-toolbar');
                    if (toolbar) {
                        toolbar.remove();
                    }
                    // Clear container content
                    editorElement.innerHTML = '';

                    // Re-initialize
                    if (window.initQuill && window.initQuill()) {
                        // Restore content if we had any
                        if (currentContent && window.quillInstance) {
                            window.quillInstance.root.innerHTML = currentContent;
                        }
                    }
                }
            }, 150); // Increased delay to ensure DOM is fully updated
        });

        // Submit event handling is done via 'livewire:submit' event below

        // Listen for clear editor event from Livewire
        window.addEventListener('clear-quill-editor', () => {
            if (window.quillInstance) {
                window.quillInstance.setText(''); // Clear the editor content
                window.quillInstance.setContents([{insert: '\n'}]); // Reset to blank
                const hiddenInput = document.getElementById('description-hidden');
                if (hiddenInput) {
                    hiddenInput.value = '';
                }
            }
        });

        // Global function to clear Quill (can be called from anywhere)
        window.clearQuillEditor = function() {
            if (window.quillInstance) {

                // Clear in multiple ways to ensure it works
                window.quillInstance.setText('');
                window.quillInstance.setContents([]);
                window.quillInstance.root.innerHTML = '<p><br></p>'; // Quill default empty state

                if (window.quillInstance.history) {
                    window.quillInstance.history.clear(); // Clear undo/redo history
                }

                // Clear the hidden input and trigger Livewire update
                const hiddenInput = document.getElementById('description-hidden');
                if (hiddenInput) {
                    hiddenInput.value = '';
                    // Trigger multiple events to ensure Livewire picks it up
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

                    // Also directly call Livewire to set the value
                    if (window.Livewire) {
                        const component = window.Livewire.find(hiddenInput.closest('[wire\\:id]')?.getAttribute('wire:id'));
                        if (component) {
                            component.set('description', '');
                        }
                    }
                }

                return true;
            } else {
                return false;
            }
        };

        // Listen for browser event as fallback
        window.addEventListener('clear-quill', () => {
            window.clearQuillEditor();
        });

        // Alternative: Listen for DOM changes (reset button click)
        document.addEventListener('click', (e) => {
            if (e.target.matches('[wire\\:click*="resetForm"]') || e.target.closest('[wire\\:click*="resetForm"]')) {
                // Clear accumulated files when form is reset
                window.accumulatedFiles = [];
                setTimeout(() => {
                    window.clearQuillEditor();
                }, 200); // Longer delay to ensure Livewire has processed
            }
        });

        // Listen for image removal from Livewire
        document.addEventListener('livewire:updated', () => {
            // Listen for image-removed event
            window.Livewire.on('image-removed', (data) => {
                console.log(`🗑️ Gambar di index ${data.index} telah dihapus dari Livewire`);
            });
        });



        // Show keyboard shortcuts hint
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                console.log('%c💡 Keyboard Shortcuts:', 'color: #3b82f6; font-weight: bold;');
                console.log('  Ctrl+S: Save form');
                console.log('  Ctrl+R: Reset form');
                console.log('  Escape: Go back');
            }, 1000);
        });
    </script>
</div>
