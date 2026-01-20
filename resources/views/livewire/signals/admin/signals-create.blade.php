<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">
            Buat Sinyal Saham Baru
        </flux:heading>
        <flux:subheading size="lg" class="mb-6">
            Tambahkan sinyal saham secara manual
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Actions Bar -->
    <flux:button variant="ghost" size="sm" class="mb-3" href="{{ route('admin.signals.index') }}" wire:navigate icon="arrow-left">
        Kembali
    </flux:button>

    <!-- Alert Messages -->
    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Create Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Sinyal</h3>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="save" class="space-y-6">
                        <!-- Signal Type & Kode Emiten -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:select wire:model="signal_type" label="Tipe Sinyal">
                                <flux:select.option value="manual">Manual</flux:select.option>
                                <flux:select.option value="technical">Technical</flux:select.option>
                                <flux:select.option value="fundamental">Fundamental</flux:select.option>
                                <flux:select.option value="momentum">Momentum</flux:select.option>
                                <flux:select.option value="value_breakthrough">Value Breakthrough</flux:select.option>
                            </flux:select>

                            <div class="relative">
                                <flux:input
                                    wire:model.live.debounce.500ms="kode_emiten"
                                    label="Kode Emiten"
                                    placeholder="Contoh: BBCA"
                                />
                                @if($kode_emiten && $companyFound)
                                    <div class="absolute right-3 top-9 flex items-center">
                                        <svg class="size-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($kode_emiten && !$companyFound)
                                    <div class="absolute right-3 top-9 flex items-center">
                                        <svg class="size-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Recommendation -->
                        <div>
                            <flux:textarea
                                wire:model="recommendation"
                                label="Rekomendasi"
                                placeholder="Jelaskan rekomendasi investasi untuk saham ini..."
                                rows="4"
                            />
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">Maksimal 2000 karakter</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <flux:textarea
                                wire:model="notes"
                                label="Catatan"
                                placeholder="Catatan tambahan tentang sinyal ini..."
                                rows="4"
                            />
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">Maksimal 1000 karakter</p>
                        </div>

                        <!-- Status & Notes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:select wire:model="status" label="Status">
                                <flux:select.option value="">Pilih Status</flux:select.option>
                                <flux:select.option value="draft">Draft</flux:select.option>
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="published">Published</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <flux:button variant="ghost" size="sm" icon="x-mark" href="{{ route('admin.signals.index') }}" wire:navigate>Batal</flux:button>
                            <flux:button type="submit" size="sm" icon="check" variant="primary" wire:loading.attr="disabled">Buat Sinyal</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Create Guide -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Panduan Pembuatan</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Tipe Sinyal:</h4>
                            <ul class="text-sm text-gray-600 dark:text-zinc-400 space-y-1">
                                <li><strong>Manual:</strong> Sinyal yang dibuat secara manual</li>
                                <li><strong>Technical:</strong> Berdasarkan analisis teknikal</li>
                                <li><strong>Fundamental:</strong> Berdasarkan analisis fundamental</li>
                                <li><strong>Momentum:</strong> Berdasarkan momentum pasar</li>
                                <li><strong>Value Breakthrough:</strong> Otomatis dari command analisis</li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Status:</h4>
                            <ul class="text-sm text-gray-600 dark:text-zinc-400 space-y-1">
                                <li><strong>Draft:</strong> Belum siap dipublikasikan</li>
                                <li><strong>Active:</strong> Siap untuk distribusi</li>
                                <li><strong>Published:</strong> Sudah dipublikasikan ke investor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
