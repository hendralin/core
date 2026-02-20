<div class="min-h-full">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Blog Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('blogs.index') }}" wire:navigate class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-offset-zinc-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Visit Blog
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Posts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Posts</p>
                    <p class="mt-2 text-3xl font-bold tabular-nums text-gray-900 dark:text-white">{{ $stats['total_posts'] }}</p>
                    <div class="mt-3 flex items-center gap-2 text-sm">
                        <span class="font-medium text-green-600 dark:text-green-400">{{ $stats['published_posts'] }} published</span>
                        <span class="text-gray-400 dark:text-gray-500">·</span>
                        <span class="font-medium text-amber-600 dark:text-amber-400">{{ $stats['draft_posts'] }} drafts</span>
                    </div>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/20">
                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Views --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Views</p>
                    <p class="mt-2 text-3xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</p>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Across all posts</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/20">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Comments --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Comments</p>
                    <p class="mt-2 text-3xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($stats['total_comments']) }}</p>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Engagement from readers</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-500/20">
                    <svg class="h-6 w-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Users (admin only) --}}
        @if($isAdmin)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                        <p class="mt-2 text-3xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($stats['total_users']) }}</p>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Registered authors & readers</p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-500/20">
                        <svg class="h-6 w-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Views Chart --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Views Last 7 Days</h2>
            <div class="h-64">
                <canvas id="viewsChart"></canvas>
            </div>
        </div>

        {{-- Most Viewed Posts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Most Viewed Posts</h2>
            <div class="space-y-4">
                @forelse($mostViewedPosts as $post)
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('blog.posts.edit', $post) }}" class="block truncate text-sm font-medium text-gray-900 transition-colors hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">
                                {{ $post->title }}
                            </a>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $post->published_at?->format('M d, Y') }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-500/25 dark:text-indigo-300">
                            {{ number_format($post->views_count) }} views
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No published posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Comments --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/80 dark:shadow-none">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Recent Comments</h2>
        <div class="space-y-4">
            @forelse($recentComments as $comment)
                <div class="flex gap-3 border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-zinc-600/70">
                    <flux:avatar circle size="lg" src="{{ $comment->user->avatar_url }}" />
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $comment->user->name }}
                            <span class="font-normal text-gray-500 dark:text-gray-400">commented on</span>
                            <a href="{{ route('blog.posts.edit', $comment->post) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                {{ Str::limit($comment->post->title, 30) }}
                            </a>
                        </p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($comment->content, 100) }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Chart data + dark mode flag for JS --}}
    <div
        id="viewsChartData"
        data-labels='@json($viewsData->pluck('date')->values()->toArray())'
        data-counts='@json($viewsData->pluck('count')->values()->toArray())'
        style="display: none;"
    ></div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            function isDark() {
                return document.documentElement.classList.contains('dark');
            }
            function chartColors(dark) {
                return {
                    border: dark ? 'rgb(129, 140, 248)' : 'rgb(99, 102, 241)',
                    fill: dark ? 'rgba(129, 140, 248, 0.12)' : 'rgba(99, 102, 241, 0.1)',
                    point: dark ? 'rgb(165, 180, 252)' : 'rgb(99, 102, 241)',
                    grid: dark ? 'rgba(113, 113, 122, 0.4)' : 'rgba(156, 163, 175, 0.4)',
                    text: dark ? 'rgb(161, 161, 170)' : 'rgb(107, 114, 128)',
                };
            }
            function initChart() {
                var ctx = document.getElementById('viewsChart');
                var chartDataEl = document.getElementById('viewsChartData');
                if (!ctx || !chartDataEl) return;
                var labels = JSON.parse(chartDataEl.dataset.labels || '[]');
                var data = JSON.parse(chartDataEl.dataset.counts || '[]');
                var dark = isDark();
                var colors = chartColors(dark);
                if (window._blogViewsChart) {
                    window._blogViewsChart.destroy();
                }
                window._blogViewsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Views',
                            data: data,
                            borderColor: colors.border,
                            backgroundColor: colors.fill,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: colors.point,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                grid: { color: colors.grid },
                                ticks: { color: colors.text }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: colors.grid },
                                ticks: { color: colors.text, precision: 0 }
                            }
                        }
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', initChart);
            document.addEventListener('livewire:navigated', initChart);
            var observer = new MutationObserver(function() {
                var dark = document.documentElement.classList.contains('dark');
                var was = document.body.getAttribute('data-blog-chart-dark');
                if (was === null || (dark ? '1' : '0') !== was) {
                    document.body.setAttribute('data-blog-chart-dark', dark ? '1' : '0');
                    initChart();
                }
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        })();
    </script>
    @endpush
</div>
