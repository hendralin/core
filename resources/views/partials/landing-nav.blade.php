<header
    class="sticky top-0 z-50 bg-white/90 dark:bg-zinc-900/90 backdrop-blur border-b border-zinc-200 dark:border-zinc-800"
    x-data="{
        dark: (function() {
            var t = localStorage.theme;
            return t === 'dark' || (t !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        })(),
        toggle() {
            this.dark = !this.dark;
            if (this.dark) {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        }
    }"
    @keydown.window="if ($event.shiftKey && ($event.key === 'd' || $event.key === 'D')) toggle()"
>
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 text-xl font-semibold text-zinc-900 dark:text-white">
                <img src="{{ asset('photos/logo/favicon-1080x1080.png') }}" alt="{{ config('app.name') }}" class="h-12 w-12 object-contain">
                {{ config('app.name') }}
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}#beranda" wire:navigate class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Beranda</a>
                <a href="{{ route('home') }}#fitur" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Fitur</a>
                <a href="{{ route('home') }}#cara-kerja" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Cara Kerja</a>
                <a href="{{ route('home') }}#testimoni" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Testimoni</a>
                <a href="{{ route('blogs.index') }}" wire:navigate class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Blogs</a>
                <a href="{{ route('home') }}#kontak" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Kontak</a>
                @auth
                    <a href="{{ url('/dashboard') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition">Daftar</a>
                    @endif
                @endauth
                <flux:separator vertical class="my-2" />
                <div class="flex items-center">
                    <flux:tooltip content="Toggle light mode">
                        <flux:button
                            icon="sun"
                            variant="ghost"
                            x-show="dark"
                            @click="toggle()"
                        />
                    </flux:tooltip>
                    <flux:tooltip content="Toggle dark mode">
                        <flux:button
                            icon="moon"
                            variant="ghost"
                            x-show="!dark"
                            @click="toggle()"
                        />
                    </flux:tooltip>
                </div>
            </div>
            {{-- Mobile menu button --}}
            <button type="button" class="md:hidden p-2 rounded-lg text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden pb-4 space-y-2">
            <a href="{{ route('home') }}#beranda" wire:navigate class="block py-2 text-zinc-600 dark:text-zinc-400">Beranda</a>
            <a href="{{ route('home') }}#fitur" class="block py-2 text-zinc-600 dark:text-zinc-400">Fitur</a>
            <a href="{{ route('home') }}#cara-kerja" class="block py-2 text-zinc-600 dark:text-zinc-400">Cara Kerja</a>
            <a href="{{ route('home') }}#testimoni" class="block py-2 text-zinc-600 dark:text-zinc-400">Testimoni</a>
            <a href="{{ route('blogs.index') }}" wire:navigate class="block py-2 text-zinc-600 dark:text-zinc-400">Blogs</a>
            <a href="{{ route('home') }}#kontak" class="block py-2 text-zinc-600 dark:text-zinc-400">Kontak</a>
            @auth
                <a href="{{ url('/dashboard') }}" wire:navigate class="block py-2 text-emerald-600 font-medium">Dashboard</a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="block py-2 text-zinc-600 dark:text-zinc-400">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" wire:navigate class="block py-2 text-emerald-600 font-medium">Daftar</a>
                @endif
            @endauth
        </div>
    </nav>
</header>
