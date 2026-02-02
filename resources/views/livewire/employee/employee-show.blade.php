@php
    use Illuminate\Support\Str;
@endphp

<div>
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
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center">
                                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Per Page:</label>
                                <select id="per-page" wire:model.live="perPage"
                                        class="text-sm rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach ($this->perPageOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <flux:spacer class="hidden md:inline" />
                            <flux:spacer class="hidden md:inline" />
                            <div class="flex items-center">
                                <label for="search-salary-components" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
                                <flux:input wire:model.live.debounce.500ms="search" id="search-salary-components" placeholder="Component name, description..." clearable />
                            </div>
                        </div>
                    </div>

                    <!-- Employee Salary Components List -->
                    @if(isset($employeeSalaryComponents) && $employeeSalaryComponents->count() > 0)
                        <div class="space-y-3">
                            @foreach($employeeSalaryComponents as $employeeSalaryComponent)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700/70 transition-colors">
                                <div class="flex-1">
                                    <flux:text class="font-medium text-gray-900 dark:text-white">{{ $employeeSalaryComponent->salaryComponent->name ?? 'N/A' }}</flux:text>
                                    <div class="flex items-center gap-4 mt-1">
                                        @if($employeeSalaryComponent->is_quantitative)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Quantitative: {{ number_format($employeeSalaryComponent->amount, 2) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                Fixed: {{ number_format($employeeSalaryComponent->amount, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($employeeSalaryComponent->description)
                                        <flux:text class="text-sm text-gray-500 dark:text-zinc-400 mt-1">{{ $employeeSalaryComponent->description }}</flux:text>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <flux:text class="text-sm text-gray-500 dark:text-zinc-400">{{ $employeeSalaryComponent->updated_at->format('M d, Y') }}</flux:text>
                                    <flux:text class="text-xs text-gray-400 dark:text-zinc-500 block">{{ $employeeSalaryComponent->updated_at->format('H:i') }}</flux:text>
                                </div>
                            </div>
                            @endforeach
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
