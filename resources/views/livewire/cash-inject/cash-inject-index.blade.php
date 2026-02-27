<div class="min-h-full">
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <flux:heading size="xl" level="1" class="text-zinc-900 dark:text-white tracking-tight">{{ __('Inject Kas') }}</flux:heading>
            <flux:subheading size="lg" class="mt-1 text-zinc-500 dark:text-zinc-400">{{ __('Kelola inject kas perusahaan') }}</flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('cash-inject.create')
                <flux:button variant="primary" size="sm" href="{{ route('cash-injects.create') }}" wire:navigate icon="plus" class="cursor-pointer">
                    Tambah Inject
                </flux:button>
            @endcan
            @can('cash-inject.audit')
                <flux:button variant="ghost" size="sm" href="{{ route('cash-injects.audit') }}" wire:navigate icon="document-text" class="cursor-pointer">
                    Audit
                </flux:button>
            @endcan
            <flux:button variant="ghost" size="sm" wire:click="exportExcel" icon="document-arrow-down" class="cursor-pointer" tooltip="Export Excel">
                Excel
            </flux:button>
            <flux:button variant="ghost" size="sm" wire:click="exportPdf" icon="document-arrow-down" class="cursor-pointer" tooltip="Export PDF">
                PDF
            </flux:button>
            <div wire:loading.delay class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400 text-sm">
                <flux:icon.loading class="w-4 h-4 text-red-600" />
            </div>
        </div>
    </div>

    @session('success')
        <flux:callout icon="check-circle" color="green" class="mb-6">{{ $value }}</flux:callout>
    @endsession

    @session('error')
        <flux:callout icon="x-circle" color="red" class="mb-6">{{ $value }}</flux:callout>
    @endsession

    {{-- Filters card --}}
    <div class="bg-white dark:bg-zinc-800/80 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6 overflow-hidden">
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/80 dark:bg-zinc-800/50">
            <div class="flex items-center gap-2">
                <flux:icon.funnel class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Filter</span>
                @if($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') || $selectedMonthYear || $costTypeFilter !== '' || $warehouseFilter !== '')
                    <flux:badge size="sm" color="blue" class="ml-1">Aktif</flux:badge>
                @endif
            </div>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <flux:input type="month"
                        label="Bulan & Tahun"
                        wire:model.live="selectedMonthYear"
                        size="sm"
                        min="2019-01"
                        max="{{ date('Y') + 1 }}-12" />
                <flux:select label="Tipe Kas" wire:model.live="costTypeFilter" class="w-full" size="sm">
                    <flux:select.option value="">Semua</flux:select.option>
                    <flux:select.option value="cash">Kas Kecil</flux:select.option>
                    <flux:select.option value="tax_cash">Kas Pajak</flux:select.option>
                </flux:select>
                <flux:select label="Warehouse" wire:model.live="warehouseFilter" class="w-full" size="sm" icon="building-storefront">
                    <flux:select.option value="">Semua</flux:select.option>
                    @foreach($warehouses as $warehouse)
                        <flux:select.option value="{{ $warehouse->id }}">{{ $warehouse->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input type="date" wire:model.live="dateFrom" label="Dari" size="sm" />
                <flux:input type="date" wire:model.live="dateTo" label="Sampai" size="sm" />
                @if($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') || $selectedMonthYear || $costTypeFilter !== '' || $warehouseFilter !== '')
                    <div class="sm:col-span-2 lg:col-span-1">
                        <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark" class="w-full cursor-pointer text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400">
                            Reset Filter
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="bg-white dark:bg-zinc-800/80 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Toolbar: per page + search --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/30">
            <div class="flex items-center gap-3">
                <label for="per-page" class="text-sm text-zinc-600 dark:text-zinc-400">Tampilkan</label>
                <flux:select id="per-page" wire:model.live="perPage" size="sm" class="w-20">
                    @foreach ($this->perPageOptions as $option)
                        <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                    @endforeach
                </flux:select>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">baris</span>
            </div>
            <div class="w-full sm:w-64">
                <flux:input
                    wire:model.live.debounce.400ms="search"
                    placeholder="Cari deskripsi..."
                    clearable
                    size="sm"
                    icon="magnifying-glass"
                />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/80 dark:bg-zinc-800/50">
                        <th scope="col" class="px-4 py-3.5 w-12 text-center">#</th>
                        <th scope="col" class="px-4 py-3.5 w-28">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer" wire:click="sortBy('cost_date')">
                                Tanggal
                                @if ($sortField === 'cost_date')
                                    @if ($sortDirection === 'asc')
                                        <flux:icon.chevron-up class="w-4 h-4" />
                                    @else
                                        <flux:icon.chevron-down class="w-4 h-4" />
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3.5 w-24">Tipe Kas</th>
                        <th scope="col" class="px-4 py-3.5 w-40">Warehouse</th>
                        <th scope="col" class="px-4 py-3.5 min-w-[200px]">Deskripsi</th>
                        <th scope="col" class="px-4 py-3.5 w-36 text-right">
                            <button type="button" class="inline-flex items-center gap-1 ml-auto hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer" wire:click="sortBy('total_price')">
                                Total
                                @if ($sortField === 'total_price')
                                    @if ($sortDirection === 'asc')
                                        <flux:icon.chevron-up class="w-4 h-4" />
                                    @else
                                        <flux:icon.chevron-down class="w-4 h-4" />
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3.5 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @if(isset($costs) && $costs->count() > 0)
                        @foreach($costs as $index => $cost)
                            <tr class="bg-white dark:bg-zinc-800/50 hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors" wire:loading.class="opacity-60">
                                <td class="px-4 py-3 text-center text-zinc-500 dark:text-zinc-400 tabular-nums">
                                    {{ $costs->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-zinc-900 dark:text-white font-medium">
                                    {{ Carbon\Carbon::parse($cost->cost_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($cost->cost_type === 'tax_cash')
                                        <flux:badge size="sm" color="amber">Kas Pajak</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="green">Kas Kecil</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-zinc-700 dark:text-zinc-300 max-w-40 truncate" title="{{ $cost->warehouse->name ?? '-' }}">
                                    {{ $cost->warehouse->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300 max-w-xs truncate" title="{{ $cost->description }}">
                                    {{ $cost->description }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap font-semibold text-zinc-900 dark:text-white tabular-nums">
                                    Rp {{ number_format($cost->total_price, 0) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-0.5">
                                        @can('cash-inject.view')
                                            <flux:button variant="ghost" size="xs" square href="{{ route('cash-injects.show', $cost) }}" wire:navigate tooltip="Lihat" class="text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400">
                                                <flux:icon.eye variant="mini" />
                                            </flux:button>
                                        @endcan
                                        @can('cash-inject.edit')
                                            <flux:button variant="ghost" size="xs" square href="{{ route('cash-injects.edit', $cost) }}" wire:navigate tooltip="Edit" class="text-zinc-500 hover:text-amber-600 dark:hover:text-amber-400">
                                                <flux:icon.pencil-square variant="mini" />
                                            </flux:button>
                                        @endcan
                                        @can('cash-inject.delete')
                                            <flux:modal.trigger name="delete-cost">
                                                <flux:button variant="ghost" size="xs" square class="cursor-pointer text-zinc-500 hover:text-red-600 dark:hover:text-red-400" wire:click="setCostToDelete({{ $cost->id }})" tooltip="Hapus">
                                                    <flux:icon.trash variant="mini" />
                                                </flux:button>
                                            </flux:modal.trigger>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-zinc-500 dark:text-zinc-400">
                                    <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon.banknotes class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-medium">
                                        @if(isset($search) && $search !== '')
                                            Tidak ada hasil untuk "{{ $search }}"
                                        @else
                                            Belum ada data inject kas
                                        @endif
                                    </p>
                                    <p class="text-xs max-w-sm">Sesuaikan filter atau tambah inject kas baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if(isset($costs) && $costs->count() > 0 && $totalForFilters > 0)
                    <tfoot>
                        <tr class="bg-emerald-50/80 dark:bg-emerald-900/20 border-t-2 border-emerald-200 dark:border-emerald-800/50">
                            <td colspan="4" class="px-4 py-3">
                                @php
                                    $activeFilters = [];
                                    if ($costTypeFilter === 'cash') {
                                        $activeFilters[] = 'Tipe: Kas Kecil';
                                    } elseif ($costTypeFilter === 'tax_cash') {
                                        $activeFilters[] = 'Tipe: Kas Pajak';
                                    }
                                    if ($warehouseFilter) {
                                        $warehouseName = $warehouses->firstWhere('id', (int) $warehouseFilter)?->name;
                                        $activeFilters[] = 'Warehouse: ' . ($warehouseName ?: $warehouseFilter);
                                    }
                                    if ($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) {
                                        $activeFilters[] = \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' – ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y');
                                    }
                                    if (empty($activeFilters)) {
                                        $activeFilters[] = 'Bulan berjalan';
                                    }
                                @endphp
                                <span class="text-xs text-emerald-700 dark:text-emerald-300">{{ implode(' · ', $activeFilters) }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-900 dark:text-emerald-100">Total</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-900 dark:text-emerald-100 tabular-nums">Rp {{ number_format($totalForFilters, 0) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if(isset($costs) && $costs->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/30 dark:bg-zinc-800/30">
                {{ $costs->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    {{-- Delete confirmation modal --}}
    <flux:modal name="delete-cost" class="min-w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus inject kas?</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                    Data inject kas ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="delete">Hapus</span>
                    <span wire:loading wire:target="delete">Menghapus...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
