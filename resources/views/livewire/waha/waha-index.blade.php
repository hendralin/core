<div class="space-y-6">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('WAHA Configuration') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Configure your WhatsApp HTTP API (WAHA) settings') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <div class="grid gap-6">
        <!-- Configuration Status Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">WAHA Configuration</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Base URL and API key settings</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(env('WAHA_API_URL') && env('WAHA_API_KEY'))
                            <flux:badge color="green" icon="check" size="sm">Configured</flux:badge>
                            @if($isConnected)
                                <flux:badge color="blue" icon="wifi" size="sm">Connected</flux:badge>
                            @else
                                <flux:badge color="red" icon="x-circle" size="sm">Disconnected</flux:badge>
                            @endif
                        @else
                            <flux:badge color="yellow" icon="x-mark" size="sm">Not Configured</flux:badge>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Base URL -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gray-200 dark:bg-zinc-600 rounded-lg">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Base URL</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">WAHA_API_URL environment variable</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(env('WAHA_API_URL'))
                                <p class="font-mono text-sm text-gray-900 dark:text-white">{{ env('WAHA_API_URL') }}</p>
                                <p class="text-xs text-green-600 dark:text-green-400">✓ Configured</p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Not set</p>
                                <p class="text-xs text-red-600 dark:text-red-400">✗ Missing</p>
                            @endif
                        </div>
                    </div>

                    <!-- API Key -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gray-200 dark:bg-zinc-600 rounded-lg">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">API Key</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">WAHA_API_KEY environment variable</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(env('WAHA_API_KEY'))
                                <p class="font-mono text-sm text-gray-900 dark:text-white">{{ Str::mask(env('WAHA_API_KEY'), '*', 8) }}</p>
                                <p class="text-xs text-green-600 dark:text-green-400">✓ Configured</p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Not set</p>
                                <p class="text-xs text-red-600 dark:text-red-400">✗ Missing</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!env('WAHA_API_URL') || !env('WAHA_API_KEY'))
                    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Configuration Required</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    You must configure WAHA_API_URL and WAHA_API_KEY in your environment variables before you can create and manage WhatsApp sessions.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Configuration Complete</h4>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                    WAHA is properly configured. You can now create and manage WhatsApp sessions.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @can('waha.edit')
        <div class="mt-6 flex justify-start">
            <flux:modal.trigger name="show-configuration">
                <flux:button variant="primary" icon="cog-6-tooth">
                    Configure WAHA
                </flux:button>
            </flux:modal.trigger>
        </div>
    @endcan

    <!-- WAHA Configuration Modal -->
    <flux:modal name="show-configuration" variant="flyout" position="right">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Configure WAHA Settings</flux:heading>
                <flux:text class="mt-2">Enter your WAHA API configuration details below.</flux:text>
            </div>

            <flux:input label="Base URL" description="The base URL of your WAHA API instance." placeholder="https://your-waha-instance.com" wire:model="wahaApiUrl" />
            <flux:input label="API Key" description="Your WAHA API authentication key." placeholder="Your WAHA API key" wire:model="wahaApiKey" type="password" viewable />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="saveConfiguration" variant="primary">
                    Save Configuration
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
