<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Inject Kas') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Kelola inject kas perusahaan') }}</flux:subheading>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Month/Year Filter -->
            <div>
                <label for="month-year" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Month & Year</label>
                <input type="month"
                       id="month-year"
                       wire:model.live="selectedMonthYear"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-200 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                       min="2019-01"
                       max="{{ date('Y') + 1 }}-12">
            </div>

            <!-- Date Period Filters -->
            <flux:input type="date" wire:model.live="dateFrom" label="From" size="sm" />
            <flux:input type="date" wire:model.live="dateTo" label="To" size="sm" />

            <!-- Clear Filters Button -->
            @if($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') || $selectedMonthYear)
            <div class="space-y-2 flex flex-col justify-end">
                <flux:button wire:click="clearFilters" variant="filled" size="sm" icon="x-mark" class="w-full cursor-pointer">
                    Clear Filter
                </flux:button>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-4 mb-2">
        <!-- Actions Section -->
        <div class="flex flex-col lg:flex-row gap-3">
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                @can('cash-inject.create')
                    <flux:button variant="primary" size="sm" href="{{ route('cash-injects.create') }}" wire:navigate icon="plus" class="w-full sm:w-auto" tooltip="Tambah Inject Kas">Tambah</flux:button>
                @endcan

                <!-- Button Actions -->
                <div class="flex gap-1">
                    @can('cash-inject.audit')
                        <flux:button variant="ghost" size="sm" href="{{ route('cash-injects.audit') }}" wire:navigate icon="document-text" class="w-full sm:w-auto" tooltip="Audit Trail">Audit</flux:button>
                    @endcan
                    <flux:button variant="ghost" size="sm" wire:click="exportExcel" icon="document-arrow-down" tooltip="Export to Excel" class="flex-1 sm:flex-none cursor-pointer">
                        <span class="hidden sm:inline">Excel</span>
                        <span class="sm:hidden">Excel</span>
                    </flux:button>
                    <flux:button variant="ghost" size="sm" wire:click="exportPdf" icon="document-arrow-down" tooltip="Export to PDF" class="flex-1 sm:flex-none cursor-pointer">
                        <span class="hidden sm:inline">PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </flux:button>

                    <div wire:loading class="flex items-center justify-center p-2">
                        <flux:icon.loading class="text-red-600 w-4 h-4" />
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
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
            <flux:input wire:model.live.debounce.500ms="search" placeholder="Cari inject kas..." clearable />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-56">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'cost_date') {{ $sortDirection }} @endif" wire:click="sortBy('cost_date')">
                            Date
                            @if ($sortField == 'cost_date' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'cost_date' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3">Description</th>
                    <th scope="col" class="px-4 py-3 text-right w-32">
                        <div class="flex items-center justify-end cursor-pointer @if ($sortField == 'total_price') {{ $sortDirection }} @endif" wire:click="sortBy('total_price')">
                            Total Price
                            @if ($sortField == 'total_price' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'total_price' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-1/12">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($costs) && $costs->count() > 0)
                    @foreach($costs as $index => $cost)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $costs->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-white">{{ Carbon\Carbon::parse($cost->cost_date)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap lg:whitespace-normal text-gray-600 dark:text-zinc-300 max-w-xs truncate" title="{{ $cost->description }}">{{ $cost->description }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">Rp {{ number_format($cost->total_price, 0) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @can('cash-inject.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('cash-injects.show', $cost) }}" wire:navigate tooltip="View Details">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('cash-inject.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('cash-injects.edit', $cost) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('cash-inject.delete')
                                    <flux:modal.trigger name="delete-cost">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setCostToDelete({{ $cost->id }})" tooltip="Delete">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="5">
                            @if(isset($search) && !empty($search))
                                No results found for "{{ $search }}"
                            @else
                                Tidak ada Inject Kas yang ditemukan.
                            @endif
                        </td>
                    </tr>
                @endforelse

                <!-- Total Footer - Always show for current period/filters -->
                @if($totalForFilters > 0)
                    <tr class="bg-blue-50 dark:bg-zinc-900/20 border-t-2 border-blue-200 dark:border-zinc-800">
                        <td colspan="2" class="px-4 py-3">
                            @php
                                $activeFilters = [];
                                if ($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) {
                                    $activeFilters[] = 'period (' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y') . ')';
                                }
                                if (empty($activeFilters)) {
                                    $activeFilters[] = 'current month';
                                }
                            @endphp
                            @if(!empty($activeFilters))
                                <div class="text-xs text-zinc-700 dark:text-zinc-200 mt-1">
                                    Filter by {{ implode(', ', $activeFilters) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">
                            TOTAL:
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-zinc-900 dark:text-zinc-100">
                            Rp {{ number_format($totalForFilters, 0) }}
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-2">
        {{ $costs->links(data: ['scrollTo' => false]) }}
    </div>


    <!-- Delete Modal -->
    <flux:modal name="delete-cost" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete cash inject record?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this cash inject record.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Delete Record</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
