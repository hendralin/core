<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('About Broadcast') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Informasi sistem dan aplikasi') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- System Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.information-circle class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    Broadcast Information
                </flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Application Name</flux:text>
                            <flux:text class="text-lg font-semibold">Broadcast</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Version</flux:text>
                            <flux:text class="text-lg font-semibold">{{ $systemInfo['version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">PHP Version</flux:text>
                            <flux:text class="text-sm">{{ $systemInfo['php_version'] }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Laravel Version</flux:text>
                            <flux:text class="text-sm">{{ $systemInfo['laravel_version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Database</flux:text>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['database']) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Environment</flux:text>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['environment']) }}</flux:text>
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Timezone</flux:text>
                        <flux:text class="text-sm">{{ $systemInfo['timezone'] }}</flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
