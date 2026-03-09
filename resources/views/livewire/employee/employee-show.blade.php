@php
    use Illuminate\Support\Str;
@endphp

<div x-data x-init="if (window.location.hash === '#histori-pinjaman') { $nextTick(() => document.getElementById('histori-pinjaman')?.scrollIntoView({ behavior: 'smooth', block: 'start' })) }">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Employee') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Employee details and salary information') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('employees.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Employees">Back</flux:button>
        @can('employee.edit')
            <flux:button variant="filled" size="sm" href="{{ route('employees.edit', $employee->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Employee Information</flux:heading>

                    <div class="space-y-4">
                        <div>
                            <flux:heading size="md">Name</flux:heading>
                            <flux:text class="mt-1">{{ $employee->name }}</flux:text>
                        </div>

                        @if($employee->user)
                        <div>
                            <flux:heading size="md">User Account</flux:heading>
                            <flux:text class="mt-1">{{ $employee->user->email }}</flux:text>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="md">Position</flux:heading>
                                <flux:text class="mt-1">{{ $employee->position->name ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="md">Status</flux:heading>
                                <flux:text class="mt-1">
                                    @if($employee->status == 1)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Active
                                        </span>
                                    @elseif($employee->status == 2)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Inactive
                                        </span>
                                    @endif
                                </flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <div>
                                <flux:heading size="sm">Join Date</flux:heading>
                                <flux:text class="text-sm">{{ $employee->join_date->format('M d, Y') }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Created</flux:heading>
                                <flux:text class="text-sm">{{ $employee->created_at->format('M d, Y \a\t H:i') }}</flux:text>
                            </div>
                        </div>

                        @if($employee->updated_at != $employee->created_at)
                        <div class="grid grid-cols-2 gap-4">
                            <div></div>
                            <div>
                                <flux:heading size="sm">Last Updated</flux:heading>
                                <flux:text class="text-sm">{{ $employee->updated_at->format('M d, Y \a\t H:i') }}</flux:text>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Histori Pinjaman -->
                @if(isset($loanHistory) && ($loanHistory->isNotEmpty() || ($remainingLoan ?? 0) > 0))
                <div id="histori-pinjaman" class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 scroll-mt-4">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                <flux:icon.banknotes class="w-5 h-5" />
                            </div>
                            <div>
                                <flux:heading size="lg">Histori Pinjaman</flux:heading>
                                <flux:subheading size="sm" class="text-gray-500 dark:text-zinc-400">Pinjaman dan pembayaran karyawan</flux:subheading>
                            </div>
                        </div>
                        @can('employee-loan-payment.create')
                            <flux:button variant="ghost" size="sm" href="{{ route('employee-loan-payments.index') }}" wire:navigate icon="arrow-top-right-on-square">
                                Kelola Pembayaran
                            </flux:button>
                        @endcan
                    </div>

                    <!-- Ringkasan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pinjaman</p>
                            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">Rp {{ number_format($totalLoans ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Seluruh waktu</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pembayaran</p>
                            <p class="mt-1 text-lg font-semibold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalPayments ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Yang sudah dibayar</p>
                        </div>
                        <div class="rounded-xl border-2 border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-900/20 p-4">
                            <p class="text-xs font-medium text-amber-700 dark:text-amber-400 uppercase tracking-wider">Sisa Pinjaman</p>
                            <p class="mt-1 text-xl font-bold text-amber-700 dark:text-amber-300">Rp {{ number_format($remainingLoan ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-amber-600/80 dark:text-amber-400/80 mt-0.5">Saat ini</p>
                        </div>
                    </div>

                    <!-- Daftar transaksi -->
                    @if($loanHistory->isNotEmpty())
                    <div class="border border-gray-200 dark:border-zinc-600 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-700/50 border-b border-gray-200 dark:border-zinc-600">
                            <flux:heading size="sm">Riwayat Transaksi</flux:heading>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-zinc-700 max-h-80 overflow-y-auto">
                            @foreach($loanHistory as $record)
                            <div class="flex items-start gap-4 px-4 py-3 hover:bg-gray-50/80 dark:hover:bg-zinc-700/30 transition-colors">
                                <div class="shrink-0 mt-0.5">
                                    @if($record->loan_type === 'loan')
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center" title="Pinjaman diterima">
                                            <flux:icon.arrow-down-tray class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center" title="Pembayaran">
                                            <flux:icon.arrow-up-tray class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            @if($record->loan_type === 'loan')
                                                Pinjaman diterima
                                            @else
                                                Pembayaran pinjaman
                                            @endif
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $record->paid_at->format('d M Y') }}</span>
                                    </div>
                                    @if($record->description)
                                        <p class="text-sm text-gray-600 dark:text-zinc-400 mt-0.5 line-clamp-2">{{ $record->description }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                        @if($record->loan_type === 'loan')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                                @if($record->big_cash) Kas Besar @else {{ $record->cost?->warehouse?->name ?? 'Kas Kecil' }} @endif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                @if($record->big_cash) Kas Besar @else {{ $record->cost?->warehouse?->name ?? 'Kas' }} @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if($record->loan_type === 'loan')
                                        <span class="font-semibold text-blue-600 dark:text-blue-400">+ Rp {{ number_format($record->amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">− Rp {{ number_format($record->amount, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50/30 dark:bg-amber-900/10 p-4 text-center">
                        <flux:icon.banknotes class="mx-auto w-10 h-10 text-amber-500 dark:text-amber-400" />
                        <flux:text class="mt-2 text-amber-800 dark:text-amber-200">Belum ada riwayat transaksi. Sisa pinjaman tercatat Rp {{ number_format($remainingLoan ?? 0, 0, ',', '.') }}.</flux:text>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Employee Salary Components -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="lg">
                            Salary Components
                            @if(!empty($search))
                                <span class="text-sm font-normal text-gray-600 dark:text-zinc-400">(filtered)</span>
                            @endif
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            @if(isset($paginationInfo))
                                @if($paginationInfo['is_filtered'])
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                        {{ $paginationInfo['start'] }}-{{ $paginationInfo['end'] }} of {{ $paginationInfo['total'] }} results
                                    </span>
                                    @if($paginationInfo['total'] != $employeeTotalSalaryComponents)
                                        <span class="text-sm text-gray-600 dark:text-zinc-400">
                                            ({{ $employeeTotalSalaryComponents }} total)
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $paginationInfo['start'] }}-{{ $paginationInfo['end'] }} of {{ $paginationInfo['total'] }} salary components
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $employeeTotalSalaryComponents }} total salary components
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Per Page Controls -->
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300 mr-2 w-16">Per Page:</label>
                                <flux:select id="per-page" wire:model.live="perPage" class="w-18">
                                    @foreach ($this->perPageOptions as $option)
                                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <flux:spacer class="hidden md:inline" />

                            <div class="flex items-center">
                                <label for="search-salary-components" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
                                <flux:input wire:model.live.debounce.500ms="search" id="search-salary-components" placeholder="Component name, description..." clearable />
                            </div>
                        </div>
                    </div>

                    <!-- Employee Salary Components List -->
                    @if(isset($employeeSalaryComponents) && $employeeSalaryComponents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700 border dark:border-zinc-700 rounded-lg">
                                <thead class="bg-gray-50 dark:bg-zinc-700/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">No.</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Component</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($employeeSalaryComponents as $employeeSalaryComponent)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $loop->iteration + ($employeeSalaryComponents->currentPage() - 1) * $employeeSalaryComponents->perPage() }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $employeeSalaryComponent->salaryComponent->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            @if($employeeSalaryComponent->is_quantitative)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    Quantitative
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                    Fixed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                            {{ number_format($employeeSalaryComponent->amount, 0) }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-500 dark:text-zinc-400">
                                            {{ $employeeSalaryComponent->description ?: '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600 dark:text-zinc-400">
                                @if(isset($paginationInfo))
                                    Page {{ $employeeSalaryComponents->currentPage() }} of {{ $employeeSalaryComponents->lastPage() }}
                                    @if($paginationInfo['is_filtered'])
                                        (filtered results)
                                    @endif
                                @endif
                            </div>
                            <div>
                                {{ $employeeSalaryComponents->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon.currency-dollar class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-600" />
                            <flux:heading size="md" class="mt-2 text-gray-600 dark:text-zinc-400">
                                @if(!empty($search))
                                    No salary components found for "{{ $search }}"
                                @else
                                    @if($employeeTotalSalaryComponents > 0)
                                        No salary components to display
                                    @else
                                        No salary components assigned to this employee
                                    @endif
                                @endif
                            </flux:heading>
                            <flux:text class="text-sm text-gray-500 dark:text-zinc-500">
                                @if(!empty($search))
                                    Try adjusting your search terms or clear the search to see all salary components.
                                @elseif($employeeTotalSalaryComponents > 0)
                                    All salary components for this employee are filtered out by current settings.
                                @else
                                    Salary components will appear here when they are assigned to this employee.
                                @endif
                            </flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="space-y-6">
                @if(isset($marketingSalesSummary) && $marketingSalesSummary && (int) ($employee->position_id ?? 0) === 1)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-blue-200 dark:border-blue-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <flux:heading size="lg">Marketing Performance</flux:heading>
                            <flux:subheading size="sm" class="text-gray-500 dark:text-zinc-400">
                                Ringkasan penjualan mobil oleh karyawan ini
                            </flux:subheading>
                        </div>
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                <flux:icon.currency-dollar class="w-5 h-5 text-blue-600 dark:text-blue-300" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total Unit Terjual</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($marketingSalesSummary['total_vehicles_sold'] ?? 0, 0, ',', '.') }} unit
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total Nilai Penjualan</p>
                                <p class="mt-1 text-lg font-semibold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($marketingSalesSummary['total_sales_amount'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Unit Terjual Bulan Ini</p>
                                <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($marketingSalesSummary['vehicles_sold_this_month'] ?? 0, 0, ',', '.') }} unit
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Rata-rata Harga Jual</p>
                                <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($marketingSalesSummary['average_selling_price'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(isset($marketingSales) && $marketingSales->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-3">
                            <flux:heading size="sm">Penjualan Terbaru</flux:heading>
                            <span class="text-xs text-gray-500 dark:text-zinc-400">
                                {{ $marketingSales->count() }} transaksi terakhir
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach($marketingSales as $sale)
                            <div class="flex items-start justify-between rounded-md border border-gray-100 dark:border-zinc-700 px-3 py-2 hover:bg-gray-50 dark:hover:bg-zinc-800/60 transition-colors">
                                <div class="min-w-0 mr-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        <a href="{{ route('vehicles.show', $sale->id) }}" wire:navigate class="hover:underline">
                                            {{ $sale->police_number ?? 'N/A' }}
                                        </a>
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-zinc-400 truncate">
                                        {{ $sale->brand?->name }} {{ $sale->vehicle_model?->name }} {{ $sale->type?->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-0.5">
                                        @if($sale->selling_date)
                                            {{ \Carbon\Carbon::parse($sale->selling_date)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($sale->selling_price ?? 0, 0, ',', '.') }}
                                    </p>
                                    @if($sale->payment_type)
                                    <p class="text-[11px] text-gray-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wide">
                                        {{ $sale->payment_type == 1 || $sale->payment_type === '1' || $sale->payment_type === 'cash' ? 'Cash' : 'Kredit' }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-zinc-700">
                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                            Belum ada data penjualan mobil yang tercatat untuk karyawan ini.
                        </p>
                    </div>
                    @endif
                </div>
                @endif

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Statistics</flux:heading>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:text>Total Employees (All)</flux:text>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                {{ $totalEmployeesCount }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Salary Components</flux:text>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                {{ $employeeTotalSalaryComponents }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Salary Records</flux:text>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                {{ $employeeTotalSalaries }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Employee Age</flux:text>
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $employee->join_date->diffForHumans() }}
                            </span>
                        </div>

                        @if($employee->updated_at != $employee->created_at)
                        <div class="flex items-center justify-between">
                            <flux:text>Last Modified</flux:text>
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $employee->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
