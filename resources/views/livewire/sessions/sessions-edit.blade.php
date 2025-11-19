<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Session') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for editing WhatsApp HTTP API session') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('sessions.show', $session) }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Session">Back</flux:button>

        <div class="w-full max-w-2xl">
            <!-- Session Information Display -->
            <div class="mt-6 space-y-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
                    <div class="flex items-start gap-3">
                        <flux:icon.information-circle class="h-6 w-6 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                        <div>
                            <flux:heading size="md" class="text-blue-900 dark:text-blue-100 mb-2">Session Information</flux:heading>
                            <flux:text class="text-blue-800 dark:text-blue-200 mb-4">
                                To change the 'Session Name or ID' - please remove the session and create again.
                            </flux:text>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <flux:text class="font-medium text-blue-900 dark:text-blue-100">Session Name:</flux:text>
                                    <flux:text class="text-blue-800 dark:text-blue-200">{{ $session->name }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="font-medium text-blue-900 dark:text-blue-100">Session ID:</flux:text>
                                    <flux:text class="text-blue-800 dark:text-blue-200">{{ $session->session_id }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
