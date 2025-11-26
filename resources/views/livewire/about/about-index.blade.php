<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('About Boilerplate') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Informasi sistem dan aplikasi') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- System Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.information-circle class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    Boilerplate Information
                </flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:heading size="sm" level="3">Application Name</flux:heading>
                            <flux:text class="text-lg font-semibold">Boilerplate</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm" level="3">Version</flux:heading>
                            <flux:text class="text-lg font-semibold">{{ $systemInfo['version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:heading size="sm" level="3">PHP Version</flux:heading>
                            <flux:text class="text-sm">{{ $systemInfo['php_version'] }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm" level="3">Laravel Version</flux:heading>
                            <flux:text class="text-sm">{{ $systemInfo['laravel_version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:heading size="sm" level="3">Database</flux:heading>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['database']) }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm" level="3">Environment</flux:heading>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['environment']) }}</flux:text>
                        </div>
                    </div>

                    <div>
                        <flux:heading size="sm" level="3">Timezone</flux:heading>
                        <flux:text class="text-sm">{{ $systemInfo['timezone'] }}</flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
