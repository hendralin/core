{{-- resources/views/components/table-filters.blade.php --}}
<div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
    <div class="space-y-4">
        <!-- Basic Filters Row -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Status Filter -->
            <div class="flex items-center">
                <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Status:</label>
                <flux:select size="sm" wire:model.live="statusFilter" id="status-filter" placeholder="">
                    <flux:select.option value="">All</flux:select.option>
                    <flux:select.option value="1">Active</flux:select.option>
                    <flux:select.option value="0">Inactive</flux:select.option>
                    <flux:select.option value="2">Pending</flux:select.option>
                </flux:select>
            </div>

            <!-- Role Filter -->
            <div class="flex items-center">
                <label for="role-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Role:</label>
                <flux:select size="sm" wire:model.live="roleFilter" id="role-filter" placeholder="">
                    <flux:select.option value="">All Roles</flux:select.option>
                    @foreach ($roles as $role)
                        <flux:select.option value="{{ $role->id }}">{{ ucwords($role->name) }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Verification Filter -->
            <div class="flex items-center">
                <label for="verification-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Email:</label>
                <flux:select size="sm" wire:model.live="verificationFilter" id="verification-filter" placeholder="">
                    <flux:select.option value="">All</flux:select.option>
                    <flux:select.option value="verified">Verified</flux:select.option>
                    <flux:select.option value="unverified">Unverified</flux:select.option>
                </flux:select>
            </div>

            <!-- Advanced Filters Toggle -->
            <button wire:click="toggleAdvancedFilters"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center cursor-pointer">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                {{ $showAdvancedFilters ? 'Hide' : 'Show' }} Advanced Filters
            </button>

            <!-- Clear Filters -->
            <button wire:click="clearFilters" class="text-sm text-blue-600 dark:text-blue-400 hover:underline ml-auto cursor-pointer">
                Clear Filters
            </button>
        </div>

        <!-- Advanced Filters Row -->
        @if($showAdvancedFilters)
        <div class="border-t border-gray-200 dark:border-zinc-600 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Created Date From -->
                <div>
                    <label for="created-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Joined From:</label>
                    <input type="date" id="created-from" wire:model.live="createdDateFrom"
                           class="w-full text-sm rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Created Date To -->
                <div>
                    <label for="created-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Joined To:</label>
                    <input type="date" id="created-to" wire:model.live="createdDateTo"
                           class="w-full text-sm rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Last Login Date From -->
                <div>
                    <label for="login-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Login From:</label>
                    <input type="date" id="login-from" wire:model.live="loginDateFrom"
                           class="w-full text-sm rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Last Login Date To -->
                <div class="md:col-span-2 lg:col-span-1">
                    <label for="login-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Login To:</label>
                    <input type="date" id="login-to" wire:model.live="loginDateTo"
                           class="w-full text-sm rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
