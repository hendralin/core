<footer class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/80 dark:bg-zinc-900/80 backdrop-blur mt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <div class="flex items-center gap-2">
            @if (auth()->user()?->company_logo)
                <img
                    src="{{ auth()->user()->company_logo_url }}"
                    alt="Company Logo"
                    class="w-8 h-8 rounded-md object-contain bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700"
                >
            @else
                <img
                    src="{{ asset('photos/logo/favicon-32x32.png') }}"
                    alt="Logo"
                    class="w-8 h-8 rounded-md bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700"
                >
            @endif
            <div>
                <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ config('app.name') }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    Sistem manajemen showroom & keuangan.
                </div>
            </div>
        </div>

        <div class="hidden md:block">
            <div class="font-semibold text-zinc-900 dark:text-zinc-100 mb-1">
                Quick Links
            </div>
            <div class="flex flex-wrap gap-3 text-[11px] text-zinc-600 dark:text-zinc-400">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                    Dashboard
                </a>
                @can('vehicle.view')
                    <a href="{{ route('vehicles.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                        Vehicles
                    </a>
                @endcan
                @can('sales-report.view')
                    <a href="{{ route('sales-reports.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                        Sales Report
                    </a>
                @endcan
            </div>
        </div>

        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 text-right self-center">
            © {{ date('Y') }} {{ config('app.name') }}.
        </div>
    </div>
</footer>
