<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Session Details') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Detailed information about WhatsApp HTTP API session') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('sessions.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Sessions">Back</flux:button>

        @session('success')
            <x-alert type="success" class="mt-4 mb-4">{{ $value }}</x-alert>
        @endsession

        @session('error')
            <x-alert type="error" class="mt-4 mb-4">{{ $value }}</x-alert>
        @endsession

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
            <!-- Session Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                        <flux:icon.device-phone-mobile class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        Session Information
                    </flux:heading>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Session Name</flux:text>
                                <flux:text class="text-lg font-semibold">{{ $session->name }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Session ID</flux:text>
                                <flux:text class="text-lg font-semibold">{{ $session->session_id }}</flux:text>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Created At</flux:text>
                                <flux:text class="text-sm">{{ $session->created_at->format('M d, Y H:i') }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Updated</flux:text>
                                <flux:text class="text-sm">{{ $session->updated_at->format('M d, Y H:i') }}</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WAHA API Information -->
                @if($sessionData)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                        <svg class="h-6 w-6 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.742.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                        WhatsApp API Information
                    </flux:heading>

                    <div class="space-y-4">
                        @if(isset($sessionData['me']))
                        <!-- Profile Picture Section -->
                        @if($profilePicture)
                        <div class="flex items-center gap-4 mb-4">
                            <div class="shrink-0">
                                <img src="{{ $profilePicture }}" alt="Profile Picture" class="w-16 h-16 rounded-full border-2 border-gray-200 dark:border-gray-700 object-cover">
                            </div>
                            <div>
                                <flux:text class="text-lg font-semibold">{{ $sessionData['me']['pushName'] ?? 'N/A' }}</flux:text>
                                <flux:text class="text-sm text-gray-600 dark:text-gray-400">{{ isset($sessionData['me']['id']) ? substr($sessionData['me']['id'], 0, -5) : 'N/A' }}</flux:text>
                            </div>
                        </div>
                        @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">WhatsApp Number</flux:text>
                                <flux:text class="text-lg font-semibold">{{ isset($sessionData['me']['id']) ? substr($sessionData['me']['id'], 0, -5) : 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Display Name</flux:text>
                                <flux:text class="text-lg font-semibold">{{ $sessionData['me']['pushName'] ?? 'N/A' }}</flux:text>
                            </div>
                        </div>
                        @endif
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">API Status</flux:text>
                                <div class="flex items-center gap-2">
                                    @if ($sessionData['status'] == 'WORKING')
                                        <flux:badge color="green">Working</flux:badge>
                                    @elseif ($sessionData['status'] == 'SCAN_QR_CODE')
                                        <flux:badge color="yellow">Scan QR Code</flux:badge>
                                    @elseif ($sessionData['status'] == 'STOPPED')
                                        <flux:badge color="gray">STOPPED</flux:badge>
                                    @elseif ($sessionData['status'] == 'STARTING')
                                        <flux:badge color="blue">STARTING</flux:badge>
                                    @elseif ($sessionData['status'] == 'UNKNOWN')
                                        <flux:badge color="gray">Unknown</flux:badge>
                                    @elseif ($sessionData['status'] == 'ERROR')
                                        <flux:badge color="red">Error</flux:badge>
                                    @else
                                        <flux:badge color="red">{{ $sessionData['status'] }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Presence</flux:text>
                                <div class="flex items-center gap-2">
                                    @if($sessionData['presence'] == 'online')
                                        <flux:badge color="green">Online</flux:badge>
                                    @elseif($sessionData['presence'] == 'offline')
                                        <flux:badge color="red">Offline</flux:badge>
                                    @else
                                        <flux:badge color="gray">{{ $sessionData['presence'] ?? 'Unknown' }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Engine</flux:text>
                                <flux:text class="text-sm">{{ $sessionData['engine']['engine'] ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Activity</flux:text>
                                <flux:text class="text-sm">
                                    @if(isset($sessionData['timestamps']['activity']))
                                        {{ \Carbon\Carbon::createFromTimestampMs($sessionData['timestamps']['activity'])->format('M d, Y H:i:s') }}
                                    @else
                                        N/A
                                    @endif
                                </flux:text>
                            </div>
                        </div>

                        @if(isset($sessionData['config']['noweb']))
                        <div>
                            <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">NOWEB Configuration</flux:text>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <flux:text class="font-medium">Mark Online:</flux:text>
                                    <flux:text>{{ $sessionData['config']['noweb']['markOnline'] ? 'Yes' : 'No' }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="font-medium">Store Enabled:</flux:text>
                                    <flux:text>{{ $sessionData['config']['noweb']['store']['enabled'] ? 'Yes' : 'No' }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="font-medium">Full Sync:</flux:text>
                                    <flux:text>{{ $sessionData['config']['noweb']['store']['fullSync'] ? 'Yes' : 'No' }}</flux:text>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 p-6">
                    <div class="flex items-center gap-3">
                        <flux:icon.exclamation-triangle class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        <div>
                            <flux:text class="font-medium text-yellow-800 dark:text-yellow-200">API Data Unavailable</flux:text>
                            <flux:text class="text-sm text-yellow-700 dark:text-yellow-300">Unable to fetch session data from WhatsApp API</flux:text>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Actions Sidebar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Actions</flux:heading>

                    <div class="space-y-3">
                        @can('session.edit')
                            <flux:button variant="filled" size="sm" href="{{ route('sessions.edit', $session) }}" wire:navigate icon="pencil-square" class="w-full">
                                Edit
                            </flux:button>
                        @endcan

                        <flux:button variant="primary" color="green" size="sm" wire:click="refreshSessionData" icon="arrow-path" class="w-full cursor-pointer">
                            Refresh
                        </flux:button>

                        @can('session.connect')
                            @if($sessionData && ($sessionData['status'] === 'FAILED' || $sessionData['status'] === 'STOPPED' || $sessionData['status'] === 'UNKNOWN'))
                                <flux:button variant="primary" color="blue" size="sm" wire:click="startSession" icon="play" class="w-full cursor-pointer">
                                    Start
                                </flux:button>
                            @elseif($sessionData && $sessionData['status'] === 'SCAN_QR_CODE')
                                <flux:modal.trigger name="qr-code-modal">
                                    <flux:button variant="primary" color="yellow" size="sm" wire:click="scanQRCode" icon="qr-code" class="w-full cursor-pointer mb-3">
                                        Scan QR Code
                                    </flux:button>
                                </flux:modal.trigger>
                            @endif
                        @endcan

                        @can('session.connect')
                            @if($sessionData && ($sessionData['status'] === 'WORKING' || $sessionData['status'] === 'STOPPED' || $sessionData['status'] === 'FAILED'))
                                <flux:button variant="primary" color="pink" size="sm" wire:click="restartSession" icon="arrow-uturn-left" class="w-full cursor-pointer">
                                    Restart
                                </flux:button>
                            @endif
                        @endcan

                        @can('session.disconnect')
                            @if($sessionData && ($sessionData['status'] === 'WORKING' || $sessionData['status'] === 'SCAN_QR_CODE'))
                                <flux:button variant="primary" color="amber" size="sm" wire:click="stopSession" icon="stop" class="w-full cursor-pointer">
                                    Stop
                                </flux:button>
                            @endif

                            @if($sessionData && ($sessionData['status'] === 'WORKING'))
                                <flux:button variant="primary" size="sm" wire:click="logoutSession" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                                    Logout
                                </flux:button>
                            @endif
                        @endcan

                        @can('session.delete')
                            <flux:modal.trigger name="delete-session-modal">
                                <flux:button variant="danger" size="sm" icon="trash" class="w-full cursor-pointer">
                                    Delete
                                </flux:button>
                            </flux:modal.trigger>
                        @endcan
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-session-modal" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Session') }}</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-600">
                    {{ __('Are you sure you want to delete this session? This action cannot be undone.') }}
                </flux:text>
            </div>

            <div class="p-3 bg-gray-50 dark:bg-gray-50 rounded-md border border-gray-200 dark:border-gray-200">
                <flux:text class="font-semibold text-gray-900 dark:text-gray-900">{{ $session->name }}</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" class="text-gray-700 dark:text-gray-700 border-gray-300 dark:border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete()"
                             class="bg-red-600 dark:bg-red-600 hover:bg-red-700 dark:hover:bg-red-700 text-white dark:text-white">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- QR Code Modal -->
    <flux:modal name="qr-code-modal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="flex items-center gap-2">
                    <flux:icon.qr-code class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                    WhatsApp QR Code
                </flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                    Scan this QR code with WhatsApp on your phone to connect this session.
                </flux:text>
            </div>

            @if($qrCodeImage)
            <div class="text-center">
                <div class="inline-block p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                    <img src="{{ $qrCodeImage }}" alt="WhatsApp QR Code" class="max-w-full h-auto rounded-md shadow-sm max-h-80">
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <flux:icon.qr-code class="h-16 w-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                <flux:text class="text-gray-600 dark:text-gray-400">
                    QR Code will appear here after scanning.
                </flux:text>
            </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">
                        Close
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
