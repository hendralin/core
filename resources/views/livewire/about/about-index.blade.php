<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('About Broadcaster') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Informasi sistem dan aplikasi') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Application Description -->
    <div class="bg-linear-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6 mb-8">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg shrink-0">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg" class="mb-2 text-gray-900 dark:text-white">About Broadcaster v1.6.0</flux:heading>
                    <flux:text class="text-gray-700 dark:text-zinc-300 leading-relaxed">
                        Broadcaster is a comprehensive WhatsApp Business messaging platform built with Laravel and powered by WAHA (WhatsApp HTTP API).
                        It provides a complete solution for managing multiple WhatsApp Business sessions, advanced message broadcasting with async queue processing, automated schedule management with recurring message delivery (Daily, Weekly, Monthly), scheduled messaging with timezone support, multiple message types (text, image, file, custom link preview), template management, contacts synchronization, and comprehensive audit trails through an intuitive web interface.
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">Core Features</flux:text>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                WhatsApp Session Management
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Scheduled Message Broadcasting
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Advanced Message Broadcasting
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Automated Schedule Management
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Scheduled Messaging
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Real-time Connection Monitoring
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Bulk Messaging with Excel
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Async Queue Processing
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">System Features</flux:text>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Advanced Template Management
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Message Broadcasting System
                                </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Role-Based Access Control
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Message Audit Trails
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Comprehensive Activity Logging
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Modern Responsive UI
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="pt-4 border-t border-blue-200 dark:border-blue-700">
                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                        Built with modern web technologies including Laravel, Livewire, Flux UI, and Tailwind CSS for optimal performance and user experience.
                        Features advanced broadcast messaging with async queue processing, automated schedule management with recurring message delivery (Daily, Weekly, Monthly), scheduled messaging with timezone support, multiple message types (text, image, file, custom link preview), advanced filtering and validation, bulk Excel upload, comprehensive audit trails, template management with real-time preview, auto retry mechanism, rate limiting, Laravel Scheduler integration, and complete contact synchronization system.
                    </flux:text>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- System Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon.information-circle class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    Broadcaster Information
                </flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:heading>Application Name</flux:heading>
                            <flux:text class="text-lg font-semibold">{{ env('APP_NAME') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading>Version</flux:heading>
                            <flux:text class="text-lg font-semibold">{{ $systemInfo['version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:heading>PHP Version</flux:heading>
                            <flux:text class="text-sm">{{ $systemInfo['php_version'] }}</flux:text>
                        </div>
                        <div>
                            <flux:heading>Laravel Version</flux:heading>
                            <flux:text class="text-sm">{{ $systemInfo['laravel_version'] }}</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:heading>Database</flux:heading>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['database']) }}</flux:text>
                        </div>
                        <div>
                            <flux:heading>Environment</flux:heading>
                            <flux:text class="text-sm">{{ ucfirst($systemInfo['environment']) }}</flux:text>
                        </div>
                    </div>

                    <div>
                        <flux:heading>Timezone</flux:heading>
                        <flux:text class="text-sm">{{ $systemInfo['timezone'] }}</flux:text>
                    </div>
                </div>
            </div>
        </div>

        <!-- WAHA Integration Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.742.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    WhatsApp Integration (WAHA)
                </flux:heading>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 rounded-lg border @if($wahaInfo['configured'] && $wahaInfo['connected']) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 @elseif($wahaInfo['configured']) bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 @else bg-gray-50 dark:bg-zinc-700/50 border-gray-200 dark:border-zinc-700 @endif">
                        <div class="flex items-center gap-3">
                            @if($wahaInfo['configured'] && $wahaInfo['connected'])
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @elseif($wahaInfo['configured'])
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="p-2 bg-gray-100 dark:bg-zinc-700 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <flux:text class="font-medium">Status</flux:text>
                                <flux:text class="text-sm">{{ $wahaInfo['status'] }}</flux:text>
                            </div>
                        </div>
                        @if($wahaInfo['configured'] && $wahaInfo['connected'])
                            <flux:badge color="green" icon="wifi">Connected</flux:badge>
                        @elseif($wahaInfo['configured'])
                            <flux:badge color="yellow" icon="x-circle">Disconnected</flux:badge>
                        @else
                            <flux:badge color="gray" icon="x-mark">Not Configured</flux:badge>
                        @endif
                    </div>

                    @if($wahaInfo['configured'])
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">API URL</flux:text>
                                    <flux:text class="text-sm font-mono break-all">{{ $wahaInfo['api_url'] }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-600 dark:text-zinc-400">Version</flux:text>
                                    <flux:text class="text-sm">{{ $wahaInfo['version'] ?? 'Unknown' }}</flux:text>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <flux:text class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Configuration Required</flux:text>
                                    <flux:text class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                        WAHA is not configured. Visit the <a href="{{ route('waha.index') }}" wire:navigate class="font-medium underline underline-offset-2 hover:text-yellow-800 dark:hover:text-yellow-100">WAHA Configuration</a> page to set it up.
                                    </flux:text>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Broadcast Messages Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon name="paper-airplane" class="h-6 w-6 text-emerald-500 dark:text-emerald-400" />
                    Broadcast Messages System
                </flux:heading>

                <div class="space-y-4">
                    <flux:text class="text-gray-700 dark:text-zinc-300">
                        Advanced message broadcasting system supporting direct messaging, template-based communications, scheduled messaging with timezone support, and multiple message types (text, image, file, custom link preview) with bulk sending capabilities through Excel uploads. Features comprehensive delivery status tracking (sent, failed, pending), scheduled message indicators, advanced filtering, and automatic resend functionality for failed messages with confirmation dialogs.
                    </flux:text>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $statistics['total_messages'] ?? 0 }}</div>
                            <div class="text-sm text-emerald-700 dark:text-emerald-300">Total Messages</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statistics['messages_today'] ?? 0 }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Sent Today</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $statistics['pending_messages'] ?? 0 }}</div>
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">Pending</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statistics['sent_messages'] ?? 0 }}</div>
                            <div class="text-sm text-green-700 dark:text-green-300">Sent</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $statistics['failed_messages'] ?? 0 }}</div>
                            <div class="text-sm text-red-700 dark:text-red-300">Failed</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">Broadcasting Features</flux:text>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Direct Message Broadcasting
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Scheduled Message Broadcasting
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Template-Based Messaging
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Bulk Excel Upload
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Advanced Filtering & Search
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Multiple Message Types
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Failed Message Resend
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Message Audit Trails
                                </li>
                            </ul>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Individual & Group Targeting
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Real-time Delivery Status
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Message Type Icons
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Timezone-aware Timestamps
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Message Status Tracking
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Async Queue Processing
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Auto Retry (3 attempts)
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Rate Limiting (5 msg/sec)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Management Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon name="document-text" class="h-6 w-6 text-purple-500 dark:text-purple-400" />
                    Template Management System
                </flux:heading>

                <div class="space-y-4">
                    <flux:text class="text-gray-700 dark:text-zinc-300">
                        Advanced template management system for creating and managing WhatsApp message templates with dynamic variables and real-time preview capabilities.
                    </flux:text>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ \App\Models\Template::count() }}</div>
                            <div class="text-sm text-purple-700 dark:text-purple-300">Total Templates</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Models\Template::where('is_active', true)->count() }}</div>
                            <div class="text-sm text-green-700 dark:text-green-300">Active Templates</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Template::sum('usage_count') }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Total Usage</div>
                        </div>
                        <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ \App\Models\Template::where('last_used_at', '>=', now()->startOfWeek())->count() }}</div>
                            <div class="text-sm text-orange-700 dark:text-orange-300">Used This Week</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">Template Features</flux:text>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Dynamic Variables (@{{1}}, @{{2}}, etc.)
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Real-time Preview
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Usage Tracking
                                </li>
                            </ul>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Template Validation
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Audit Trail
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    WhatsApp Formatting
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacts Management Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon name="user-circle" class="h-6 w-6 text-cyan-500 dark:text-cyan-400" />
                    Contacts Management & Sync
                </flux:heading>

                <div class="space-y-4">
                    <flux:text class="text-gray-700 dark:text-zinc-300">
                        Advanced contact management system with automatic synchronization from WhatsApp sessions, profile picture caching, and comprehensive filtering capabilities.
                    </flux:text>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200 dark:border-cyan-800">
                            <div class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ \App\Models\Contact::count() }}</div>
                            <div class="text-sm text-cyan-700 dark:text-cyan-300">Total Contacts</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Models\Contact::whereNotNull('verified_name')->count() }}</div>
                            <div class="text-sm text-green-700 dark:text-green-300">Verified Contacts</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Session::active()->count() }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Active Sessions</div>
                        </div>
                        <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ \App\Models\Contact::whereNotNull('profile_picture_url')->count() }}</div>
                            <div class="text-sm text-orange-700 dark:text-orange-300">With Photos</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">Contacts Features</flux:text>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    WhatsApp API Sync
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Profile Picture Caching
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Advanced Filtering
                                </li>
                            </ul>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Session-based Organization
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Verification Status Tracking
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Audit Trail & Logging
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue System Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon name="queue-list" class="h-6 w-6 text-teal-500 dark:text-teal-400" />
                    Queue System & Async Processing
                </flux:heading>

                <div class="space-y-4">
                    <flux:text class="text-gray-700 dark:text-zinc-300">
                        Advanced asynchronous message processing system using Laravel Queue for efficient bulk messaging with automatic retry mechanism and rate limiting to prevent API overload.
                    </flux:text>

                    <div class="flex items-center justify-between p-4 rounded-lg border @if($queueInfo['configured'] && $queueInfo['connection'] !== 'sync') bg-teal-50 dark:bg-teal-900/20 border-teal-200 dark:border-teal-800 @else bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 @endif">
                        <div class="flex items-center gap-3">
                            @if($queueInfo['configured'] && $queueInfo['connection'] !== 'sync')
                                <div class="p-2 bg-teal-100 dark:bg-teal-900/30 rounded-lg">
                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <flux:text class="font-medium">Queue Status</flux:text>
                                <flux:text class="text-sm">{{ $queueInfo['status'] }}</flux:text>
                            </div>
                        </div>
                        @if($queueInfo['configured'] && $queueInfo['connection'] !== 'sync')
                            <flux:badge color="teal" icon="check-circle">Configured</flux:badge>
                        @else
                            <flux:badge color="yellow" icon="exclamation-triangle">Synchronous Mode</flux:badge>
                        @endif
                    </div>

                    @if($queueInfo['configured'] && $queueInfo['connection'] !== 'sync')
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-200 dark:border-teal-800">
                                <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ ucfirst($queueInfo['connection']) }}</div>
                                <div class="text-sm text-teal-700 dark:text-teal-300">Connection Type</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $queueInfo['pending_jobs'] ?? 0 }}</div>
                                <div class="text-sm text-yellow-700 dark:text-yellow-300">Pending Jobs</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $queueInfo['failed_jobs'] ?? 0 }}</div>
                                <div class="text-sm text-red-700 dark:text-red-300">Failed Jobs</div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <flux:text class="font-semibold text-gray-900 dark:text-white">Queue Features</flux:text>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Asynchronous Processing
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Auto Retry (3 attempts)
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Exponential Backoff
                                    </li>
                                </ul>
                                <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Rate Limiting (5 msg/sec)
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Non-blocking Requests
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Status Auto-update
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Timezone-aware Scheduling
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <flux:text class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Queue Not Configured</flux:text>
                                    <flux:text class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                        Queue is set to synchronous mode. For better performance with bulk messaging, configure <code class="px-1 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 rounded">QUEUE_CONNECTION=database</code> in your <code class="px-1 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 rounded">.env</code> file and start a queue worker.
                                    </flux:text>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Groups Management Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon name="user-group" class="h-6 w-6 text-indigo-500 dark:text-indigo-400" />
                    Groups Management & Sync
                </flux:heading>

                <div class="space-y-4">
                    <flux:text class="text-gray-700 dark:text-zinc-300">
                        Advanced group and community management system with automatic synchronization from WhatsApp sessions, participant information, and profile picture integration.
                    </flux:text>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $statistics['groups'] ?? 0 }}</div>
                            <div class="text-sm text-indigo-700 dark:text-indigo-300">Total Groups</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $statistics['communities'] ?? 0 }}</div>
                            <div class="text-sm text-purple-700 dark:text-purple-300">Communities</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statistics['regular_groups'] ?? 0 }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Regular Groups</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statistics['contacts'] ?? 0 }}</div>
                            <div class="text-sm text-green-700 dark:text-green-300">Linked Contacts</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">Groups Features</flux:text>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    WhatsApp Groups API Sync
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Community vs Group Detection
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Participant Information
                                </li>
                            </ul>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Profile Picture Preview
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Admin Role Identification
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contact Name Resolution
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedules Management Information -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon name="clock" class="h-6 w-6 text-amber-500 dark:text-amber-400" />
                    Schedules Management System
                </flux:heading>

                <div class="space-y-4">
                    <flux:text class="text-gray-700 dark:text-zinc-300">
                        Automated message scheduling system with recurring delivery options (Daily, Weekly, Monthly), timezone support, and Laravel Scheduler integration for reliable message delivery at specified times.
                    </flux:text>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $statistics['schedules'] ?? 0 }}</div>
                            <div class="text-sm text-amber-700 dark:text-amber-300">Total Schedules</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statistics['active_schedules'] ?? 0 }}</div>
                            <div class="text-sm text-green-700 dark:text-green-300">Active Schedules</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statistics['total_schedule_runs'] ?? 0 }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Total Executions</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ \App\Models\Schedule::where('frequency', 'daily')->count() }}</div>
                            <div class="text-sm text-purple-700 dark:text-purple-300">Daily Schedules</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <flux:text class="font-semibold text-gray-900 dark:text-white">Schedule Features</flux:text>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Recurring Schedules (Daily, Weekly, Monthly)
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    User Timezone Support
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Laravel Scheduler Integration
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Message Formatting Support
                                </li>
                            </ul>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-zinc-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Usage Count Tracking
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Last/Next Run Tracking
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Complete Audit Trail
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contact, Group & Number Support
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
