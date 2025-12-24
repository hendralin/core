@php
    use Illuminate\Support\Str;
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('System activity and statistics summary') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1">
                <flux:heading size="sm" class="text-gray-900 dark:text-white mb-2">Period Filter</flux:heading>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 flex-1 sm:max-w-md">
                <div class="flex-1">
                    <flux:label for="startDate" class="text-sm">Start Date</flux:label>
                    <flux:input
                        type="date"
                        wire:model.live="startDate"
                        id="startDate"
                        class="mt-1"
                    />
                </div>
                <div class="flex-1">
                    <flux:label for="endDate" class="text-sm">End Date</flux:label>
                    <flux:input
                        type="date"
                        wire:model.live="endDate"
                        id="endDate"
                        class="mt-1"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="bg-linear-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg" class="mb-2 text-gray-900 dark:text-white">
                    Welcome, {{ Auth::user()->name }}! 👋
                </flux:heading>
                <flux:text class="text-gray-700 dark:text-zinc-300">
                    Here is a summary of your Broadcaster system activity and statistics.
                </flux:text>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <div class="text-sm text-gray-600 dark:text-zinc-400">{{ $currentTime->format('l, d F Y') }}</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $currentTime->format('H:i') }}</div>
                    <div class="text-sm text-gray-500 dark:text-zinc-500 mt-1">{{ $userTimezone }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Messages -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Total Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($statistics['total_messages']) }}</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">
                        <span class="text-green-600 dark:text-green-400">{{ $statistics['messages_today'] }}</span> today
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sent Messages -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Sent Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($statistics['sent_messages']) }}</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">
                        @if($statistics['total_messages'] > 0)
                            {{ number_format(($statistics['sent_messages'] / $statistics['total_messages']) * 100, 1) }}% success
                        @else
                            0% success
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Messages -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Pending Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($statistics['pending_messages']) }}</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">
                        In queue
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Failed Messages -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Failed Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($statistics['failed_messages']) }}</p>
                    <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">
                        Needs attention
                    </p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Contacts -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">Contacts</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_contacts']) }}</p>
                </div>
            </div>
        </div>

        <!-- Groups -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">Groups</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_groups']) }}</p>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">Active Sessions</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['active_sessions']) }}</p>
                </div>
            </div>
        </div>

        <!-- Active Schedules -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">Active Schedules</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['active_schedules']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- WAHA Server & Queue Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- WAHA Server Status -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">WAHA Server Status</flux:heading>
                <div class="flex items-center gap-2">
                    @if($wahaInfo['status_color'] === 'green')
                        <span class="flex h-3 w-3 rounded-full bg-green-500"></span>
                    @elseif($wahaInfo['status_color'] === 'yellow')
                        <span class="flex h-3 w-3 rounded-full bg-yellow-500"></span>
                    @else
                        <span class="flex h-3 w-3 rounded-full bg-red-500"></span>
                    @endif
                    <span class="text-sm font-medium
                        @if($wahaInfo['status_color'] === 'green') text-green-600 dark:text-green-400
                        @elseif($wahaInfo['status_color'] === 'yellow') text-yellow-600 dark:text-yellow-400
                        @else text-red-600 dark:text-red-400
                        @endif">
                        {{ $wahaInfo['status'] }}
                    </span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-zinc-400">Configuration</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $wahaInfo['configured'] ? 'Configured' : 'Not Configured' }}
                    </span>
                </div>
                @if($wahaInfo['configured'])
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-zinc-400">API URL</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate ml-4 max-w-xs">
                            {{ Str::limit($wahaInfo['api_url'], 40) }}
                        </span>
                    </div>
                    @if($wahaInfo['version'])
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">Version</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $wahaInfo['version'] }}</span>
                        </div>
                    @endif
                @endif
                @if(!$wahaInfo['configured'])
                    <div class="mt-4">
                        <a href="{{ route('waha.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>
                            Configure WAHA Server →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Queue Status -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Queue Status</flux:heading>
                <div class="flex items-center gap-2">
                    @if($queueInfo['status_color'] === 'green')
                        <span class="flex h-3 w-3 rounded-full bg-green-500"></span>
                    @elseif($queueInfo['status_color'] === 'yellow')
                        <span class="flex h-3 w-3 rounded-full bg-yellow-500"></span>
                    @else
                        <span class="flex h-3 w-3 rounded-full bg-red-500"></span>
                    @endif
                    <span class="text-sm font-medium
                        @if($queueInfo['status_color'] === 'green') text-green-600 dark:text-green-400
                        @elseif($queueInfo['status_color'] === 'yellow') text-yellow-600 dark:text-yellow-400
                        @else text-red-600 dark:text-red-400
                        @endif">
                        {{ $queueInfo['status'] }}
                    </span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-zinc-400">Connection</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $queueInfo['connection'] }}</span>
                </div>
                @if($queueInfo['connection'] === 'database')
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-zinc-400">Pending Jobs</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($queueInfo['pending_jobs']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-zinc-400">Failed Jobs</span>
                        <span class="text-sm font-medium
                            @if($queueInfo['failed_jobs'] > 0) text-red-600 dark:text-red-400
                            @else text-gray-900 dark:text-white
                            @endif">
                            {{ number_format($queueInfo['failed_jobs']) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Message Trends Chart -->
    @php
        $totalTrendCount = array_sum(array_column($messageTrends, 'count'));
        $hasTrendData = $totalTrendCount > 0;
    @endphp
    @if($hasTrendData)
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm mb-6">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">
                    Message Trends
                    @if($startDate && $endDate && count($messageTrends) > 0)
                        @php
                            $firstTrendDate = \Carbon\Carbon::parse($messageTrends[0]['date'], $userTimezone);
                            $lastTrendDate = \Carbon\Carbon::parse($messageTrends[count($messageTrends) - 1]['date'], $userTimezone);
                        @endphp
                        ({{ $firstTrendDate->format('d M') }} - {{ $lastTrendDate->format('d M Y') }})
                    @elseif($startDate && $endDate)
                        ({{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
                    @else
                        (Last 7 Days)
                    @endif
                </flux:heading>
            </div>
            <div class="space-y-4">
                @php
                    $maxCount = max(array_column($messageTrends, 'count'));
                    $maxCount = $maxCount > 0 ? $maxCount : 1; // Prevent division by zero
                    $trendCount = count($messageTrends);
                    $gapSize = $trendCount > 7 ? 'gap-1' : 'gap-2';
                @endphp
                <div class="flex items-end justify-between {{ $gapSize }} overflow-x-auto" style="height: 200px; min-height: 200px;">
                    @foreach($messageTrends as $trend)
                        @php
                            $height = $maxCount > 0 ? ($trend['count'] / $maxCount) * 100 : 0;
                            $barHeight = max($height, 3); // Minimum 3% height for visibility
                        @endphp
                        <div class="flex flex-col items-center justify-end h-full gap-2 {{ $trendCount > 7 ? 'min-w-[40px]' : 'flex-1' }}">
                            <div class="w-full flex items-end justify-center relative" style="flex: 1 1 0; min-height: 0;">
                                <div
                                    class="w-full {{ $trendCount > 7 ? 'max-w-full' : 'max-w-[85%]' }} bg-blue-500 dark:bg-blue-400 rounded-t transition-all duration-300 hover:bg-blue-600 dark:hover:bg-blue-500 cursor-pointer relative group"
                                    style="height: {{ $barHeight }}%; min-height: 12px;"
                                    title="{{ $trend['label'] }}: {{ $trend['count'] }} messages"
                                >
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 text-sm px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                                        {{ $trend['count'] }} messages
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-zinc-400 text-center mt-2 w-full">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $trend['count'] }}</div>
                                <div class="text-[10px] mt-1 text-gray-500 dark:text-zinc-500">{{ \Carbon\Carbon::parse($trend['date'], $userTimezone)->format('d/m') }}</div>
                                @if($trendCount <= 7)
                                    <div class="text-[9px] mt-0.5 text-gray-400 dark:text-zinc-600">{{ \Carbon\Carbon::parse($trend['date'], $userTimezone)->format('D') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Activity & Upcoming Schedules -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Messages -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Recent Messages</flux:heading>
                <a href="{{ route('messages.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>
                    View All →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentMessages as $message)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-zinc-900/50 rounded-lg">
                        <div class="shrink-0">
                            @if($message->status === 'sent')
                                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @elseif($message->status === 'failed')
                                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $message->received_number ?? 'N/A' }}
                                </p>
                                <span class="text-sm text-gray-500 dark:text-zinc-500">
                                    {{ $message->created_at->setTimezone($userTimezone)->diffForHumans() }}
                                </span>
                            </div>
                            @php
                                // Check if message is JSON (image, file, custom, etc.)
                                $messageData = json_decode($message->message, true);
                                $isImage = is_array($messageData) && isset($messageData['type']) && $messageData['type'] === 'image';
                                $isFile = is_array($messageData) && isset($messageData['type']) && $messageData['type'] === 'file';
                                $isCustom = is_array($messageData) && isset($messageData['type']) && $messageData['type'] === 'custom';

                                if ($isImage || $isFile) {
                                    // For image/file messages, show caption or filename
                                    $displayText = !empty($messageData['caption'])
                                        ? $messageData['caption']
                                        : ($messageData['filename'] ?? ($isImage ? 'Image' : 'File'));
                                    $msg = Str::limit($displayText, 50);

                                    // Apply formatting for caption (only if caption exists, not for filename)
                                    if (!empty($messageData['caption'])) {
                                        // Replace *text* with <strong>text</strong>
                                        $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);
                                        // Replace _text_ with <em>text</em>
                                        $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                    }
                                } elseif ($isCustom) {
                                    // For custom messages, show text with preview URL
                                    $displayText = $messageData['text'] ?? 'Custom Link Preview';
                                    $msg = Str::limit($displayText, 50);
                                    // Apply formatting
                                    $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);
                                    $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                } else {
                                    // For text messages, apply formatting
                                    $msg = Str::limit($message->message ?? 'No message', 50);
                                    // Replace *text* with <strong>text</strong>
                                    $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);
                                    // Replace _text_ with <em>text</em>
                                    $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                }
                            @endphp
                            <div class="flex items-center justify-between gap-2 mt-1">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    @if($isImage)
                                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($isFile)
                                        <svg class="w-3 h-3 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    @elseif($isCustom)
                                        <svg class="w-3 h-3 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    @endif
                                    <p class="text-sm text-gray-600 dark:text-zinc-400 truncate">
                                        {!! $msg !!}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium shrink-0
                                    @if($message->status === 'sent') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($message->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @endif">
                                    {{ ucfirst($message->status ?? 'pending') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 dark:text-zinc-400">No messages</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Schedules -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Upcoming Schedules</flux:heading>
                <a href="{{ route('schedules.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>
                    View All →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($upcomingSchedules as $schedule)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-zinc-900/50 rounded-lg">
                        <div class="shrink-0">
                            <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $schedule->name }}
                                </p>
                                <span class="text-sm text-gray-500 dark:text-zinc-500">
                                    {{ $schedule->next_run ? $schedule->next_run->setTimezone($userTimezone)->diffForHumans() : 'N/A' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                                Frequency: <span class="font-medium">{{ ucfirst($schedule->frequency) }}</span>
                            </p>
                            @if($schedule->next_run)
                                <p class="text-sm text-gray-500 dark:text-zinc-500 mt-1">
                                    {{ $schedule->next_run->setTimezone($userTimezone)->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 dark:text-zinc-400">No upcoming schedules</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Sessions -->
    @if($activeSessions->count() > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Active Sessions</flux:heading>
                <a href="{{ route('sessions.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>
                    View All →
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeSessions as $session)
                    <div class="p-4 bg-gray-50 dark:bg-zinc-900/50 rounded-lg border border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $session->name }}</p>
                            <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-zinc-400 mb-3">{{ $session->session_id }}</p>

                        <!-- Account Info -->
                        <div class="flex items-center justify-between space-x-3">
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <div class="shrink-0">
                                    @if(isset($session->profile_picture) && $session->profile_picture)
                                        <img src="{{ $session->profile_picture }}" alt="Profile Picture" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-zinc-600 flex items-center justify-center">
                                            <flux:icon.user class="h-6 w-6 text-gray-500 dark:text-zinc-400" />
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    @if(isset($session->me) && is_array($session->me) && isset($session->me['id']) && isset($session->me['pushName']))
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900 dark:text-zinc-100">{{ $session->me['pushName'] }}</div>
                                            <div class="text-gray-400 dark:text-zinc-400">{{ $session->me['id'] }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-zinc-400">Not available</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-zinc-500 shrink-0">
                                <span>{{ $session->messages_count }} messages</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
