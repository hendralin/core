<footer id="kontak" class="bg-zinc-900 dark:bg-zinc-950 text-zinc-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <h3 class="text-white font-semibold text-lg mb-4">{{ config('app.name') }}</h3>
                <p class="text-sm">Platform analisis kuantitatif untuk trading saham dengan success rate tinggi.</p>
            </div>
            <div>
                <h4 class="text-white font-medium mb-4">Kontak Kami</h4>
                <ul class="space-y-2 text-sm">
                    <li>Hubungi kami melalui dashboard</li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-medium mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" wire:navigate class="hover:text-white transition">Beranda</a></li>
                    <li><a href="{{ route('home') }}#fitur" class="hover:text-white transition">Fitur</a></li>
                    <li><a href="{{ route('home') }}#cara-kerja" class="hover:text-white transition">Cara Kerja</a></li>
                    <li><a href="{{ route('home') }}#testimoni" class="hover:text-white transition">Testimoni</a></li>
                    <li><a href="{{ route('blogs.index') }}" wire:navigate class="hover:text-white transition">Blogs</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-medium mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Disclaimer</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-zinc-800 mt-8 pt-8 text-center text-sm text-zinc-500">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</footer>
