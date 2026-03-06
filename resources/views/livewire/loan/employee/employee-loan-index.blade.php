<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Pinjaman Karyawan') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Kelola pinjaman karyawan dari Kas') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Filter Section -->
    <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4 mb-6 border border-gray-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
            <div>
                <label for="month-year" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Bulan & Tahun</label>
                <input type="month"
                       id="month-year"
                       wire:model.live="selectedMonthYear"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-200 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                       min="2019-01"
                       max="{{ date('Y') + 1 }}-12">
            </div>

            <flux:input type="date" wire:model.live="dateFrom" label="Dari" size="sm" />
            <flux:input type="date" wire:model.live="dateTo" label="Sampai" size="sm" />

            <div class="space-y-1" x-data="{ open: false }" @click.away="open = false">
                <label class="flux-label block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Karyawan</label>
                @if($selectedEmployee ?? null)
                    <div class="flex items-center gap-2 min-h-9 px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800">
                        <span class="flex-1 min-w-0 text-zinc-900 dark:text-white text-sm whitespace-nowrap overflow-hidden text-ellipsis">{{ $selectedEmployee->name }}</span>
                        <flux:button type="button" variant="ghost" size="xs" wire:click="clearEmployeeFilter" class="shrink-0">Semua</flux:button>
                    </div>
                @else
                    <div class="relative">
                        <flux:input
                            type="text"
                            wire:model.live.debounce.300ms="employeeFilterSearch"
                            placeholder="Cari karyawan..."
                            size="sm"
                            class="w-full mt-2"
                            @focus="open = true"
                        />
                        <div x-show="open"
                             x-transition
                             class="absolute z-20 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <button type="button"
                                    wire:click="setEmployeeFilter('')"
                                    wire:key="emp-filter-all"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-left text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700/50 transition-colors border-b border-zinc-100 dark:border-zinc-700 whitespace-nowrap">
                                {{ __('Semua Karyawan') }}
                            </button>
                            @forelse($employees as $emp)
                                <button type="button"
                                        wire:click="setEmployeeFilter({{ $emp->id }})"
                                        wire:key="emp-filter-{{ $emp->id }}"
                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-left text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700/50 transition-colors border-b border-zinc-100 dark:border-zinc-700 last:border-b-0 whitespace-nowrap overflow-hidden text-ellipsis min-w-0">
                                    {{ $emp->name }}
                                </button>
                            @empty
                                <div class="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                    {{ strlen(trim($employeeFilterSearch ?? '')) > 0 ? 'Tidak ada karyawan yang cocok.' : 'Ketik untuk mencari karyawan.' }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            <flux:select wire:model.live="warehouseFilter" label="Kas" size="sm" class="w-full" icon="building-storefront">
                <flux:select.option value="">{{ __('Semua Kas') }}</flux:select.option>
                @foreach($warehouses as $wh)
                    <flux:select.option value="{{ $wh->id }}">{{ $wh->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="bigCashFilter" label="Sumber" size="sm" class="w-full">
                <flux:select.option value="">{{ __('Semua') }}</flux:select.option>
                <flux:select.option value="0">{{ __('Kas Kecil') }}</flux:select.option>
                <flux:select.option value="1">{{ __('Kas Besar') }}</flux:select.option>
            </flux:select>

            @if($employeeFilter || $warehouseFilter || $bigCashFilter !== '' || $selectedMonthYear)
                <div class="space-y-2 flex flex-col justify-end">
                    <flux:button wire:click="clearFilters" variant="filled" size="sm" icon="x-mark" class="w-full cursor-pointer">
                        Reset Filter
                    </flux:button>
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-4 mb-2">
        <div class="flex flex-col lg:flex-row gap-3">
            <div class="flex flex-wrap gap-2">
                @can('employee-loan.create')
                    <flux:button variant="primary" size="sm" href="{{ route('employee-loans.create') }}" wire:navigate icon="plus" class="w-full sm:w-auto" tooltip="Tambah Pinjaman">Tambah</flux:button>
                @endcan

                <div class="flex gap-1">
                    @can('employee-loan.audit')
                        <flux:button variant="ghost" size="sm" href="{{ route('employee-loans.audit') }}" wire:navigate icon="document-text" class="w-full sm:w-auto" tooltip="Audit Trail">Audit</flux:button>
                    @endcan

                    <div wire:loading class="flex items-center justify-center p-2">
                        <flux:icon.loading class="text-blue-600 w-4 h-4" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 mb-3 mt-4">
        <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">Show:</label>
            <flux:select id="per-page" wire:model.live="perPage">
                @foreach ($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">entries</label>
        </div>
        <flux:spacer class="hidden md:inline" />
        <flux:spacer class="hidden md:inline" />
        <div class="flex items-center">
            <label for="search" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
            <flux:input wire:model.live.debounce.500ms="search" placeholder="Employee name, description..." clearable id="search" />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-36">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('paid_at')">
                            Tanggal
                            @if ($sortField == 'paid_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'paid_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-44">Karyawan</th>
                    <th scope="col" class="px-4 py-3">Deskripsi</th>
                    <th scope="col" class="px-4 py-3 w-28">Sumber</th>
                    <th scope="col" class="px-4 py-3 text-right w-36">
                        <div class="flex items-center justify-end cursor-pointer" wire:click="sortBy('amount')">
                            Jumlah
                            @if ($sortField == 'amount' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'amount' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($loans) && $loans->count() > 0)
                    @foreach($loans as $index => $loan)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $loans->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-white">{{ $loan->paid_at->format('d-m-Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-zinc-300">{{ $loan->employee?->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap lg:whitespace-normal text-gray-600 dark:text-zinc-300 max-w-xs truncate" title="{{ $loan->description ?? '-' }}">{{ $loan->description ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-400">
                                @if($loan->big_cash)
                                    <flux:badge size="sm" color="blue">Kas Besar</flux:badge>
                                @else
                                    <flux:badge size="sm" color="green">{{ $loan->cost?->warehouse?->name ?? 'Kas Kecil' }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @can('employee-loan.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('employee-loans.show', $loan) }}" wire:navigate tooltip="Lihat">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('employee-loan.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('employee-loans.edit', $loan) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('employee-loan.delete')
                                    <flux:modal.trigger name="delete-loan">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setLoanToDelete({{ $loan->id }})" tooltip="Hapus">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="7">
                            @if(isset($search) && !empty($search))
                                Tidak ada hasil untuk "{{ $search }}"
                            @else
                                Belum ada data pinjaman.
                            @endif
                        </td>
                    </tr>
                @endif

                @if(isset($totalForFilters) && $totalForFilters > 0)
                    <tr class="bg-blue-50 dark:bg-zinc-900/20 border-t-2 border-blue-200 dark:border-zinc-800">
                        <td colspan="5" class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">
                            TOTAL:
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-zinc-900 dark:text-zinc-100">
                            Rp {{ number_format($totalForFilters, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-2">
        {{ $loans->links(data: ['scrollTo' => false]) }}
    </div>

    <!-- Delete Modal -->
    <flux:modal name="delete-loan" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus pinjaman?</flux:heading>
                <flux:text class="mt-2">
                    <p>Data pinjaman akan dihapus. Sisa pinjaman karyawan akan dikurangi. Jika pinjaman dari Kas Kecil, record kas kecil juga akan dihapus.</p>
                    <p>Tindakan ini tidak dapat dibatalkan.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
