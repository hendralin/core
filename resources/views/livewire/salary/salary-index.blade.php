<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Penggajian') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Kelola gaji karyawan per periode') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <flux:callout variant="success" icon="check-circle" class="mb-4" heading="{{ $value }}" />
    @endsession

    @session('error')
        <flux:callout variant="error" icon="exclamation-circle" class="mb-4" heading="{{ $value }}" />
    @endsession

    <div class="space-y-4 mb-2">
        <div class="flex flex-wrap gap-2">
            @can('salary.create')
                <flux:button variant="primary" size="sm" href="{{ route('salaries.create') }}" wire:navigate icon="plus" tooltip="Buat Penggajian">Buat Penggajian</flux:button>
            @endcan

            @can('salary.audit')
                <flux:button variant="ghost" size="sm" href="{{ route('salaries.audit') }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
            @endcan

            <div wire:loading>
                <flux:icon.loading class="text-red-600" />
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-3 mt-4">
        <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">Show:</label>
            <flux:select id="per-page" wire:model.live="perPage">
                @foreach ($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
            <span class="text-sm text-gray-700 dark:text-zinc-300">entries</span>
        </div>
        <flux:spacer class="md:hidden inline-block" />
        <flux:select wire:model.live="filterMonth">
            <flux:select.option value="">Semua Bulan</flux:select.option>
            @foreach($this->monthOptions as $num => $label)
                <flux:select.option value="{{ $num }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="filterYear">
            <flux:select.option value="">Semua Tahun</flux:select.option>
            @foreach($this->yearOptions as $y => $label)
                <flux:select.option value="{{ $y }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="filterEmployee">
            <flux:select.option value="">Semua Karyawan</flux:select.option>
            @foreach($employees as $emp)
                <flux:select.option value="{{ $emp->id }}">{{ $emp->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:input id="search-input" wire:model.live.debounce.500ms="search" placeholder="Cari nama karyawan..." clearable class="flex-1" />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('employee_id')">
                            Karyawan
                            @if($sortField === 'employee_id')
                                @if($sortDirection === 'asc')
                                    <flux:icon.chevron-up class="ml-2 size-4" />
                                @else
                                    <flux:icon.chevron-down class="ml-2 size-4" />
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3">Jabatan</th>
                    <th scope="col" class="px-4 py-3">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('salary_date')">
                            Periode Gaji
                            @if($sortField === 'salary_date')
                                @if($sortDirection === 'asc')
                                    <flux:icon.chevron-up class="ml-2 size-4" />
                                @else
                                    <flux:icon.chevron-down class="ml-2 size-4" />
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 text-right">Total Gaji</th>
                    <th scope="col" class="px-4 py-3 w-1/12">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($salaries) && $salaries->count() > 0)
                    @foreach($salaries as $index => $salary)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:key="salary-{{ $salary->id }}">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $salaries->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="text-gray-600 dark:text-zinc-300 font-medium">{{ $salary->employee?->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                {{ $salary->employee?->position?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                {{ $salary->salary_date ? $salary->salary_date->format('F Y') : '-' }}
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap text-gray-600 dark:text-zinc-300 font-medium">
                                Rp {{ number_format($salary->total_salary ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @can('salary.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('salaries.show', $salary) }}" wire:navigate tooltip="Lihat">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan
                                @can('salary.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('salaries.edit', $salary) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan
                                @can('salary.print')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('salaries.print', $salary) }}" target="_blank" tooltip="Cetak Slip Gaji">
                                        <flux:icon.printer variant="mini" class="text-blue-500 dark:text-blue-300" />
                                    </flux:button>
                                @endcan
                                @can('salary.delete')
                                    <flux:modal.trigger name="delete-salary">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setSalaryToDelete({{ $salary->id }})" tooltip="Hapus">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="6">
                            @if(!empty($search) || $filterMonth || $filterYear || $filterEmployee)
                                Tidak ada data yang cocok dengan filter.
                            @else
                                Belum ada data penggajian. @can('salary.create')<a href="{{ route('salaries.create') }}" wire:navigate class="text-blue-600 dark:text-blue-400 underline">Buat penggajian</a>@endcan
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4 mb-2">
        {{ $salaries->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:modal name="delete-salary" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus data gaji?</flux:heading>
                <flux:text class="mt-2">
                    Data gaji dan detail akan dihapus. Tindakan ini tidak dapat dibatalkan.
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
