<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Tambah Inject Kas') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Tambahkan informasi inject kas perusahaan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Form Content -->
        <div class="xl:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <flux:button
                    variant="primary"
                    size="sm"
                    href="{{ route('cash-injects.index') }}"
                    wire:navigate
                    icon="arrow-uturn-left"
                    tooltip="Kembali ke Inject Kas"
                >
                    Back
                </flux:button>
            </div>

            <form wire:submit="submit" id="cash-inject-form" class="mt-6 space-y-6" enctype="multipart/form-data">
                <!-- Cash Inject Information Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.identification class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <flux:heading size="md">Informasi Dasar Inject</flux:heading>
                            <flux:subheading size="sm">Data identitas dan waktu inject kas</flux:subheading>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <flux:input wire:model="cost_date" type="date" label="Tanggal Inject Kas" class="w-full" />
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Kapan inject kas dilakukan?</p>
                        </div>

                        <div class="space-y-1">
                            <flux:input
                                wire:model="total_price"
                                mask:dynamic="$money($input)"
                                icon="currency-dollar"
                                label="Total Inject (Rp)"
                                placeholder="180.000"
                                class="w-full"
                                helper="Masukkan jumlah inject kas"
                            />
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Total inject kas</p>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.document-text class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <div>
                            <flux:heading size="md">Detail Inject</flux:heading>
                            <flux:subheading size="sm">Deskripsi lengkap inject kas yang dilakukan</flux:subheading>
                        </div>
                    </div>

                    <div class="mt-6">
                        <flux:textarea
                            wire:model="description"
                            label="Deskripsi Inject"
                            placeholder="Masukkan detail inject kas yang dilakukan, keperluan apa, dan catatan tambahan..."
                            rows="4"
                        />
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.document-text class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        <div>
                            <flux:heading size="md">Dokumentasi (Opsional)</flux:heading>
                            <flux:subheading size="sm">Upload dokumen pendukung inject kas</flux:subheading>
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
                    <flux:button variant="ghost" href="{{ route('cash-injects.index') }}" wire:navigate>
                        <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="plus">
                        <span wire:loading.remove wire:target="submit">Simpan Inject Kas</span>
                        <span wire:loading wire:target="submit">Menyimpan...</span>
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Sidebar with Form Validation Errors -->
        <div class="xl:col-span-1">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon.exclamation-triangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                    <div>
                        <flux:heading size="md">Validasi Form</flux:heading>
                        <flux:subheading size="sm">Status pengisian form</flux:subheading>
                    </div>
                </div>

                @if($errors->any())
                    <div class="space-y-2">
                        @foreach($errors->all() as $error)
                            <div class="flex items-start gap-2 text-sm">
                                <flux:icon.exclamation-circle class="w-4 h-4 text-red-500 dark:text-red-400 mt-0.5 shrink-0" />
                                <span class="text-red-700 dark:text-red-400">{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-green-700 dark:text-green-400">
                        <flux:icon.check-circle class="w-4 h-4 inline mr-2" />
                        Form telah diisi dengan benar.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
