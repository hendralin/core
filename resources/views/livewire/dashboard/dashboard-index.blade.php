@php
    use Illuminate\Support\Str;
@endphp

<div>
    <!-- Hero Header Section -->
    <div class="relative overflow-hidden rounded-2xl mb-8 bg-linear-to-br from-slate-900 via-indigo-950 to-slate-900">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative px-8 py-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-xs font-medium text-cyan-300">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                        </span>
                        System Online
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-white tracking-tight">
                        Welcome back, <span class="bg-linear-to-r from-cyan-400 via-blue-400 to-purple-400 bg-clip-text text-transparent">{{ Auth::user()->name }}</span> 👋
                    </h1>
                    <p class="text-slate-300 text-lg max-w-xl">
                        Here's your broadcast command center. Monitor, analyze, and optimize your messaging campaigns.
                    </p>
                </div>

                <div class="hidden md:flex flex-col items-start lg:items-end gap-3">
                    <div class="px-5 py-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10"
                        x-data="{
                            time: '',
                            day: '',
                            date: '',
                            timezone: '{{ $userTimezone }}',
                            init() {
                                this.updateTime();
                                setInterval(() => this.updateTime(), 1000);
                            },
                            updateTime() {
                                const now = new Date();
                                const options = { timeZone: this.timezone };

                                // Format time HH:mm:ss
                                this.time = now.toLocaleTimeString('en-GB', { ...options, hour: '2-digit', minute: '2-digit', second: '2-digit' });

                                // Format day (e.g., Wednesday)
                                this.day = now.toLocaleDateString('en-US', { ...options, weekday: 'long' });

                                // Format date (e.g., 24 December 2025)
                                this.date = now.toLocaleDateString('en-GB', { ...options, day: 'numeric', month: 'long', year: 'numeric' });
                            }
                        }">
                        <div class="text-right">
                            <div class="text-sm text-slate-400 font-medium" x-text="day">{{ $currentTime->format('l') }}</div>
                            <div class="text-2xl font-bold text-white tabular-nums" x-text="time">{{ $currentTime->format('H:i:s') }}</div>
                            <div class="text-sm text-slate-500" x-text="date">{{ $currentTime->format('d F Y') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $userTimezone }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-8">
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 p-6 shadow-lg shadow-gray-200/20 dark:shadow-none">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="absolute inset-0 bg-linear-to-r from-violet-600 to-indigo-600 rounded-xl blur opacity-40"></div>
                        <div class="relative p-3 bg-linear-to-r from-violet-600 to-indigo-600 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                        </div>
                </div>
                <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Analytics Period</h2>
                        <p class="text-sm text-gray-500 dark:text-zinc-400">Filter your dashboard metrics by date range</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 lg:w-auto w-full">
                    <div class="flex-1 lg:w-48">
                    <flux:input
                        type="date"
                            label="From"
                        wire:model.live="startDate"
                        id="startDate"
                    />
                </div>
                    <div class="flex-1 lg:w-48">
                    <flux:input
                        type="date"
                            label="To"
                        wire:model.live="endDate"
                        id="endDate"
                    />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Messages -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-lg">
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
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 shadow-lg">
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

    <!-- System Status Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- WAHA Server Status -->
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-zinc-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-linear-to-br from-green-100 to-emerald-100 dark:from-green-900/40 dark:to-emerald-900/40">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">WAHA Server Status</h3>
                    </div>
                    <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full
                        @if($wahaInfo['status_color'] === 'green') bg-emerald-100 dark:bg-emerald-900/40
                        @elseif($wahaInfo['status_color'] === 'yellow') bg-amber-100 dark:bg-amber-900/40
                        @else bg-red-100 dark:bg-red-900/40
                        @endif">
                        <span class="relative flex h-2.5 w-2.5">
                            @if($wahaInfo['status_color'] === 'green')
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            @endif
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5
                                @if($wahaInfo['status_color'] === 'green') bg-emerald-500
                                @elseif($wahaInfo['status_color'] === 'yellow') bg-amber-500
                                @else bg-red-500
                                @endif"></span>
                        </span>
                        <span class="text-sm font-medium
                            @if($wahaInfo['status_color'] === 'green') text-emerald-700 dark:text-emerald-300
                            @elseif($wahaInfo['status_color'] === 'yellow') text-amber-700 dark:text-amber-300
                            @else text-red-700 dark:text-red-300
                        @endif">
                        {{ $wahaInfo['status'] }}
                    </span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-500 dark:text-zinc-400">Configuration</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $wahaInfo['configured'] ? 'Configured' : 'Not Configured' }}
                    </span>
                </div>
                @if($wahaInfo['configured'])
                    <div class="flex justify-between items-center py-2 border-t border-gray-100 dark:border-zinc-700/50">
                        <span class="text-sm text-gray-500 dark:text-zinc-400">API URL</span>
                        <span class="text-sm font-mono text-gray-900 dark:text-white truncate ml-4">
                            {{ Str::limit($wahaInfo['api_url'], 40) }}
                        </span>
                    </div>
                    @if($wahaInfo['version'])
                        <div class="flex justify-between items-center py-2 border-t border-gray-100 dark:border-zinc-700/50">
                            <span class="text-sm text-gray-500 dark:text-zinc-400">Version</span>
                            <span class="text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $wahaInfo['version'] }}</span>
                        </div>
                    @endif
                @else
                    <a href="{{ route('waha.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors" wire:navigate>
                        <span>Configure WAHA Server</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Queue Status -->
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-zinc-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-linear-to-br from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Queue Status</h3>
                    </div>
                    <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full
                        @if($queueInfo['status_color'] === 'green') bg-emerald-100 dark:bg-emerald-900/40
                        @elseif($queueInfo['status_color'] === 'yellow') bg-amber-100 dark:bg-amber-900/40
                        @else bg-red-100 dark:bg-red-900/40
                        @endif">
                        <span class="relative flex h-2.5 w-2.5">
                            @if($queueInfo['status_color'] === 'green')
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            @endif
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5
                                @if($queueInfo['status_color'] === 'green') bg-emerald-500
                                @elseif($queueInfo['status_color'] === 'yellow') bg-amber-500
                                @else bg-red-500
                                @endif"></span>
                        </span>
                        <span class="text-sm font-medium
                            @if($queueInfo['status_color'] === 'green') text-emerald-700 dark:text-emerald-300
                            @elseif($queueInfo['status_color'] === 'yellow') text-amber-700 dark:text-amber-300
                            @else text-red-700 dark:text-red-300
                        @endif">
                        {{ $queueInfo['status'] }}
                    </span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-500 dark:text-zinc-400">Driver</span>
                    <span class="text-sm font-mono font-semibold text-gray-900 dark:text-white uppercase">{{ $queueInfo['connection'] }}</span>
                </div>
                @if($queueInfo['connection'] === 'database')
                    <div class="flex justify-between items-center py-2 border-t border-gray-100 dark:border-zinc-700/50">
                        <span class="text-sm text-gray-500 dark:text-zinc-400">Pending Jobs</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-sm font-semibold
                            @if($queueInfo['pending_jobs'] > 0) bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300
                            @else bg-gray-100 text-gray-700 dark:bg-zinc-700 dark:text-zinc-300
                            @endif">
                            {{ number_format($queueInfo['pending_jobs']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-t border-gray-100 dark:border-zinc-700/50">
                        <span class="text-sm text-gray-500 dark:text-zinc-400">Failed Jobs</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-sm font-semibold
                            @if($queueInfo['failed_jobs'] > 0) bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                            @else bg-gray-100 text-gray-700 dark:bg-zinc-700 dark:text-zinc-300
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
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 shadow-lg mb-8 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-zinc-700/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-linear-to-br from-cyan-100 to-blue-100 dark:from-cyan-900/40 dark:to-blue-900/40">
                            <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Message Trends</h3>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">
                            @if($startDate && $endDate && count($messageTrends) > 0)
                                @php
                                    $firstTrendDate = \Carbon\Carbon::parse($messageTrends[0]['date'], $userTimezone);
                                    $lastTrendDate = \Carbon\Carbon::parse($messageTrends[count($messageTrends) - 1]['date'], $userTimezone);
                                @endphp
                                    {{ $firstTrendDate->format('d M') }} - {{ $lastTrendDate->format('d M Y') }}
                            @else
                                Last 7 Days
                            @endif
                            </p>
                        </div>
                    </div>
                    <div class="md:flex items-center gap-4 text-sm hidden">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-linear-to-r from-cyan-500 to-blue-500"></div>
                            <span class="text-gray-600 dark:text-zinc-400">Messages</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @php
                    $maxCount = max(array_column($messageTrends, 'count'));
                    $maxCount = $maxCount > 0 ? $maxCount : 1;
                    $trendCount = count($messageTrends);
                @endphp
                <div class="flex items-end justify-between gap-2 overflow-x-auto" style="height: 220px; min-height: 220px;">
                    @foreach($messageTrends as $index => $trend)
                        @php
                            $height = $maxCount > 0 ? ($trend['count'] / $maxCount) * 100 : 0;
                            $barHeight = max($height, 4);
                        @endphp
                        <div class="flex flex-col items-center justify-end h-full gap-3 {{ $trendCount > 7 ? 'min-w-[50px]' : 'flex-1' }}" style="animation: fadeInUp 0.5s ease-out {{ $index * 0.05 }}s both;">
                            <div class="w-full flex items-end justify-center relative" style="flex: 1 1 0; min-height: 0;">
                                <div
                                    class="w-full {{ $trendCount > 7 ? 'max-w-[40px]' : 'max-w-[60px]' }} bg-linear-to-t from-cyan-500 to-blue-500 rounded-t-lg transition-all duration-300 hover:from-cyan-400 hover:to-blue-400 cursor-pointer relative group shadow-lg shadow-cyan-500/20"
                                    style="height: {{ $barHeight }}%; min-height: 16px;"
                                >
                                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-semibold px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap pointer-events-none z-10 shadow-lg">
                                        {{ $trend['count'] }} messages
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900 dark:border-t-white"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center w-full space-y-1">
                                <div class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">{{ $trend['count'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-zinc-500 font-medium">{{ \Carbon\Carbon::parse($trend['date'], $userTimezone)->format('d/m') }}</div>
                                @if($trendCount <= 7)
                                    <div class="text-[10px] text-gray-400 dark:text-zinc-600 uppercase tracking-wide">{{ \Carbon\Carbon::parse($trend['date'], $userTimezone)->format('D') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Messages -->
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-zinc-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-linear-to-br from-violet-100 to-purple-100 dark:from-violet-900/40 dark:to-purple-900/40">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Messages</h3>
                    </div>
                    <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors" wire:navigate>
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-zinc-700/50">
                @forelse($recentMessages as $message)
                    <div class="p-4 hover:bg-gray-50/50 dark:hover:bg-zinc-700/30 transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 mt-0.5">
                            @if($message->status === 'sent')
                                    <div class="w-9 h-9 rounded-xl bg-linear-to-br from-emerald-400 to-green-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @elseif($message->status === 'failed')
                                    <div class="w-9 h-9 rounded-xl bg-linear-to-br from-red-400 to-rose-500 flex items-center justify-center shadow-lg shadow-red-500/30">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @else
                                    <div class="w-9 h-9 rounded-xl bg-linear-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/30">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $message->received_number ?? 'N/A' }}
                                </p>
                                    <span class="text-xs text-gray-500 dark:text-zinc-500 whitespace-nowrap">
                                    {{ $message->created_at->setTimezone($userTimezone)->diffForHumans() }}
                                </span>
                            </div>
                            @php
                                $messageData = json_decode($message->message, true);
                                $isImage = is_array($messageData) && isset($messageData['type']) && $messageData['type'] === 'image';
                                $isFile = is_array($messageData) && isset($messageData['type']) && $messageData['type'] === 'file';
                                $isCustom = is_array($messageData) && isset($messageData['type']) && $messageData['type'] === 'custom';

                                if ($isImage || $isFile) {
                                    $displayText = !empty($messageData['caption'])
                                        ? $messageData['caption']
                                        : ($messageData['filename'] ?? ($isImage ? 'Image' : 'File'));
                                    $msg = Str::limit($displayText, 50);
                                    if (!empty($messageData['caption'])) {
                                        $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);
                                        $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                    }
                                } elseif ($isCustom) {
                                    $displayText = $messageData['text'] ?? 'Custom Link Preview';
                                    $msg = Str::limit($displayText, 50);
                                    $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);
                                    $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                } else {
                                    $msg = Str::limit($message->message ?? 'No message', 50);
                                    $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);
                                    $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                }
                            @endphp
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
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                    @if($message->status === 'sent') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300
                                    @elseif($message->status === 'failed') bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                                    @else bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300
                                    @endif">
                                    {{ ucfirst($message->status ?? 'pending') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-zinc-700 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">No messages yet</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Messages will appear here once sent</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Schedules -->
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-zinc-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-linear-to-br from-amber-100 to-orange-100 dark:from-amber-900/40 dark:to-orange-900/40">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Schedules</h3>
                    </div>
                    <a href="{{ route('schedules.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors" wire:navigate>
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-zinc-700/50">
                @forelse($upcomingSchedules as $schedule)
                    <div class="p-4 hover:bg-gray-50/50 dark:hover:bg-zinc-700/30 transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 mt-0.5">
                                <div class="w-9 h-9 rounded-xl bg-linear-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/30">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $schedule->name }}
                                </p>
                                    <span class="text-xs text-gray-500 dark:text-zinc-500 whitespace-nowrap">
                                    {{ $schedule->next_run ? $schedule->next_run->setTimezone($userTimezone)->diffForHumans() : 'N/A' }}
                                </span>
                            </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        {{ ucfirst($schedule->frequency) }}
                                    </span>
                            @if($schedule->next_run)
                                        <span class="text-xs text-gray-500 dark:text-zinc-500">
                                            {{ $schedule->next_run->setTimezone($userTimezone)->format('d M, H:i') }}
                                        </span>
                            @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-zinc-700 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">No schedules yet</p>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Create a schedule to automate your broadcasts</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Sessions -->
    @if($activeSessions->count() > 0)
        <div class="bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-zinc-700/50 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-zinc-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-linear-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Sessions</h3>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">{{ $activeSessions->count() }} connected</p>
                        </div>
                    </div>
                    <a href="{{ route('sessions.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors" wire:navigate>
                        Manage Sessions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeSessions as $session)
                        <div class="relative group p-5 rounded-xl bg-linear-to-br from-gray-50 to-gray-100/50 dark:from-zinc-900/50 dark:to-zinc-800/50 border border-gray-200/80 dark:border-zinc-700/80 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                            <!-- Online indicator -->
                            <div class="absolute top-4 right-4">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 ring-2 ring-white dark:ring-zinc-800"></span>
                                </span>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white truncate pr-6">{{ $session->name }}</h4>
                                <p class="text-xs font-mono text-gray-500 dark:text-zinc-500 truncate mt-0.5">{{ $session->session_id }}</p>
                        </div>

                        <!-- Account Info -->
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-white/60 dark:bg-zinc-800/60">
                                <div class="shrink-0">
                                    @if(isset($session->profile_picture) && $session->profile_picture)
                                        <img src="{{ $session->profile_picture }}" alt="Profile" class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-zinc-700 shadow-md">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-linear-to-br from-gray-200 to-gray-300 dark:from-zinc-600 dark:to-zinc-700 flex items-center justify-center ring-2 ring-white dark:ring-zinc-700 shadow-md">
                                            <flux:icon.user class="h-5 w-5 text-gray-500 dark:text-zinc-400" />
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    @if(isset($session->me) && is_array($session->me) && isset($session->me['id']) && isset($session->me['pushName']))
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $session->me['pushName'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-zinc-500 truncate">{{ $session->me['id'] }}</p>
                                    @else
                                        <p class="text-xs text-gray-400 dark:text-zinc-500">Not available</p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-200/80 dark:border-zinc-700/80">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-zinc-500">Messages</span>
                                    <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($session->messages_count) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
