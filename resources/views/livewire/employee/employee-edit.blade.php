<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Employee') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit employee') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('employees.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Employees">Back</flux:button>

        <div class="w-full max-w-4xl">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <!-- Basic Employee Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="md" class="mb-4">Employee Information</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:select wire:model.live="user_id" label="User Account (Optional)">
                            <flux:select.option value="">No user account</flux:select.option>
                            @foreach($availableUsers as $user)
                                <flux:select.option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="name" label="Employee Name" placeholder="Employee name..." helper="Name will auto-update when user is selected, but can be edited manually" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <flux:input wire:model="join_date" type="date" label="Join Date" />

                        <flux:select wire:model="position_id" label="Position">
                            <flux:select.option value="">Select position...</flux:select.option>
                            @foreach($positions as $position)
                                <flux:select.option value="{{ $position->id }}">{{ $position->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="status" label="Status">
                            <flux:select.option value="1">Active</flux:select.option>
                            <flux:select.option value="2">Pending</flux:select.option>
                            <flux:select.option value="0">Inactive</flux:select.option>
                        </flux:select>
                    </div>
                </div>

                <!-- Salary Components Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="md">Salary Components</flux:heading>
                        <flux:button wire:click="addSalaryComponent" variant="ghost" size="sm" type="button" class="cursor-pointer" icon="plus">
                            Add Component
                        </flux:button>
                    </div>

                    <div class="space-y-4">
                        @if(count($selectedSalaryComponents) > 0)
                            @foreach($selectedSalaryComponents as $index => $component)
                                <div class="border border-gray-200 dark:border-zinc-600 rounded-lg p-4 bg-gray-50 dark:bg-zinc-700/50">
                                    <div class="flex items-start justify-between mb-4">
                                        <flux:heading size="sm">Salary Component {{ $index + 1 }}</flux:heading>
                                        <flux:button wire:click="removeSalaryComponent({{ $index }})" variant="ghost" size="xs" type="button" class="cursor-pointer text-red-500 hover:text-red-700">
                                            <flux:icon.trash class="w-4 h-4" />
                                        </flux:button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <flux:select wire:model="selectedSalaryComponents.{{ $index }}.salary_component_id" label="Component">
                                            <flux:select.option value="">Select component...</flux:select.option>
                                            @foreach($salaryComponents as $salaryComponent)
                                                <flux:select.option value="{{ $salaryComponent->id }}">{{ $salaryComponent->name }}</flux:select.option>
                                            @endforeach
                                        </flux:select>

                                        <div class="flex items-end">
                                            <flux:select wire:model="selectedSalaryComponents.{{ $index }}.is_quantitative" label="Type">
                                                <flux:select.option value="0">Fixed Amount</flux:select.option>
                                                <flux:select.option value="1">Quantitative</flux:select.option>
                                            </flux:select>
                                        </div>

                                        <flux:input wire:model="selectedSalaryComponents.{{ $index }}.amount" mask:dynamic="$money($input)" label="Amount" placeholder="0.00" />

                                        <flux:input wire:model="selectedSalaryComponents.{{ $index }}.description" label="Description" placeholder="Optional description..." />
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-zinc-400">
                                <flux:icon.currency-dollar class="mx-auto h-12 w-12 mb-4" />
                                <p>No salary components added yet. Click "Add Component" to get started.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" class="cursor-pointer">Update Employee</flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
