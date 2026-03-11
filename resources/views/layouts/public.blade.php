<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        @stack('css')
    </head>
    <body class="min-h-screen bg-gray-50 dark:bg-zinc-900 text-gray-900 dark:text-zinc-100">
        @php
            $company = \App\Models\Company::first();
        @endphp

        <!-- Public Navbar -->
        <header class="border-b border-gray-200 dark:border-zinc-800 bg-white/90 dark:bg-zinc-900/90 backdrop-blur sticky top-0 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                    @if ($company?->logo)
                        <img
                            src="{{ asset('logos/' . $company->logo) }}"
                            alt="{{ $company->name }}"
                            class="w-8 h-8 rounded-md object-contain bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700"
                        >
                    @else
                        <img
                            src="{{ asset('photos/logo/favicon-32x32.png') }}"
                            alt="{{ config('app.name') }}"
                            class="w-8 h-8 rounded-md bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700"
                        >
                    @endif
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-900 dark:text-zinc-100">
                            {{ $company->name ?? config('app.name') }}
                        </span>
                        @if ($company?->tagline)
                            <span class="text-[11px] text-gray-500 dark:text-zinc-400 hidden sm:inline">
                                {{ $company->tagline }}
                            </span>
                        @endif
                    </div>
                </a>

                <nav class="flex items-center gap-4 text-xs sm:text-sm">
                    <a href="{{ route('home') }}" class="hidden sm:inline text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                        Katalog
                    </a>
                    <a href="#footer-contact" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400">
                        Hubungi Kami
                    </a>
                    <!-- Theme switch -->
                    <div class="hidden sm:flex items-center gap-2 text-[11px] text-gray-600 dark:text-zinc-400">
                        <div class="flex items-center gap-1" x-data>
                            <flux:tooltip content="Toggle light mode">
                                <flux:button
                                    icon="sun"
                                    variant="ghost"
                                    x-show="$flux.dark"
                                    @click="$flux.dark = false; localStorage.theme = 'light';"
                                />
                            </flux:tooltip>
                            <flux:tooltip content="Toggle dark mode">
                                <flux:button
                                    icon="moon"
                                    variant="ghost"
                                    x-show="!$flux.dark"
                                    @click="$flux.dark = true; localStorage.theme = 'dark';"
                                />
                            </flux:tooltip>
                        </div>
                    </div>

                    @if (Route::has('dashboard') && auth()->check())
                        <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-zinc-200 hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                            Dashboard
                        </a>
                    @elseif (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" wire:navigate>
                            Login
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <div class="min-h-[calc(100vh-3.5rem)] flex flex-col">
            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer id="footer-contact" class="border-t border-gray-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
                    <div class="flex items-start gap-3">
                        @if ($company?->logo)
                            <img
                                src="{{ asset('logos/' . $company->logo) }}"
                                alt="{{ $company->name }}"
                                class="w-12 h-12 rounded-md object-contain bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700"
                            >
                        @else
                            <img
                                src="{{ asset('photos/logo/favicon-32x32.png') }}"
                                alt="{{ config('app.name') }}"
                                class="w-12 h-12 rounded-md bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700"
                            >
                        @endif
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-zinc-100">
                                {{ $company->name ?? config('app.name') }}
                            </div>
                            <p class="mt-1 text-xs text-gray-600 dark:text-zinc-400">
                                Dealer kendaraan bekas dan baru. Menyediakan berbagai pilihan mobil dengan harga transparan.
                            </p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-zinc-400">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100 mb-2">
                            Showroom
                        </div>
                        <ul class="space-y-1 text-xs text-gray-600 dark:text-zinc-400">
                            <li>
                                Jl. Residen Abdul Rozak No.123, Bukit Sangkal, Kec. Kalidoni, Palembang, Sumatera Selatan 30163
                                <br>
                                (Satu Komplek Ruko Dapur Mutiara & Rechese factory)
                            </li>
                        </ul>
                    </div>

                    <div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100 mb-2">
                            Hubungi Kami
                        </div>
                        <ul class="space-y-1 text-xs text-gray-600 dark:text-zinc-400">
                            @if ($company)
                            <li>
                                WhatsApp
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->phone) }}?text={{ urlencode('Halo, saya ingin menanyakan tentang kendaraan yang tersedia.') }}"
                                   target="_blank"
                                   class="text-green-600 hover:text-green-800 underline"
                                   rel="noopener">
                                    {{ $company->phone }}
                                </a>
                            </li>
                            <li>
                                Email:
                                <a href="mailto:{{ $company->email }}" class="text-blue-600 hover:underline">
                                    {{ $company->email }}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100 mb-2">
                            Quick Links
                        </div>
                        <ul class="space-y-1 text-xs">
                            <li>
                                <a href="{{ route('home') }}" class="text-gray-600 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                                    Katalog Kendaraan
                                </a>
                            </li>
                            @if (Route::has('dashboard') && auth()->check())
                                <li>
                                    <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                                        Dashboard
                                    </a>
                                </li>
                            @endif
                            @if (Route::has('login') && !auth()->check())
                                <li>
                                    <a href="{{ route('login') }}" class="text-gray-600 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400" wire:navigate>
                                        Login
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </footer>
        </div>

        @fluxScripts

        @stack('scripts')
    </body>
</html>

