<div>
    {{-- Hero --}}
    <section id="beranda" class="relative overflow-hidden bg-linear-to-br from-emerald-600 via-emerald-700 to-teal-800 dark:from-emerald-800 dark:via-emerald-900 dark:to-teal-950 text-white">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-80"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="text-center max-w-4xl mx-auto">
            <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider mb-4">Analisis Kuantitatif</p>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                Trading Saham
            </h1>
            <p class="text-xl text-emerald-50/95 mb-10 max-w-2xl mx-auto">
                Platform analisis kuantitatif yang memudahkan Anda memilih saham berdasarkan perhitungan matematika, statistik, dan pola yang terbukti memberikan profit terbaik dengan success rate tinggi.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ url('/dashboard') }}" wire:navigate class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-white text-emerald-700 font-semibold hover:bg-emerald-50 transition shadow-lg">Dashboard</a>
                @else
                    <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-white text-emerald-700 font-semibold hover:bg-emerald-50 transition shadow-lg">Daftar Sekarang</a>
                @endauth
                <a href="#cara-kerja" class="inline-flex items-center justify-center px-8 py-4 rounded-xl border-2 border-white/80 text-white font-semibold hover:bg-white/10 transition">Pelajari Lebih</a>
            </div>
        </div>
    </div>
    </section>

{{-- Stats --}}
    <section class="py-12 lg:py-16 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-3xl lg:text-4xl font-bold text-emerald-600 dark:text-emerald-500">30+</p>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">Metode Analisis</p>
            </div>
            <div>
                <p class="text-3xl lg:text-4xl font-bold text-emerald-600 dark:text-emerald-500">900+</p>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">Emiten Tersedia</p>
            </div>
            <div>
                <p class="text-3xl lg:text-4xl font-bold text-emerald-600 dark:text-emerald-500">1200+</p>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">User Ritel</p>
            </div>
            <div>
                <p class="text-3xl lg:text-4xl font-bold text-emerald-600 dark:text-emerald-500">4</p>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">User Korporasi</p>
            </div>
        </div>
    </div>
    </section>

{{-- Why Choose --}}
    <section class="py-16 lg:py-24 bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white mb-4">Mengapa Memilih {{ config('app.name') }}?</h2>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                STOP trading tanpa analisis! Gunakan {{ config('app.name') }} untuk memilih saham dengan fundamental baik dan berpotensi profit 1-2% per hari.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">Platform Trading Terdepan</h3>
                <p class="text-zinc-600 dark:text-zinc-400">Teknologi analisis kuantitatif terdepan untuk rekomendasi trading yang akurat. Success rate di atas 80%.</p>
                <ul class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <li class="flex items-center gap-2">• Analisis berdasarkan data historis</li>
                    <li class="flex items-center gap-2">• Success rate di atas 80%</li>
                    <li class="flex items-center gap-2">• Interface yang mudah digunakan</li>
                </ul>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">Data Lengkap & Akurat</h3>
                <p class="text-zinc-600 dark:text-zinc-400">Data backtest yang komprehensif membantu Anda menganalisis saham pilihan dengan akurasi tinggi.</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">Success Rate Tinggi</h3>
                <p class="text-zinc-600 dark:text-zinc-400">Rekomendasi saham dengan success rate di atas 80% berdasarkan analisis kuantitatif yang terbukti.</p>
            </div>
        </div>
    </div>
    </section>

{{-- Cara Kerja --}}
    <section id="cara-kerja" class="py-16 lg:py-24 bg-white dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white mb-4">Cara Kerja {{ config('app.name') }}</h2>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                Tiga langkah mudah untuk memulai trading saham dengan analisis kuantitatif
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-10">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-emerald-600 dark:text-emerald-400">1</div>
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">Pilih Metode Analisis</h3>
                <p class="text-zinc-600 dark:text-zinc-400">Pilih dari 30+ metode analisis kuantitatif yang sesuai dengan gaya trading atau investasi Anda.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-emerald-600 dark:text-emerald-400">2</div>
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">Review Rekomendasi</h3>
                <p class="text-zinc-600 dark:text-zinc-400">Dapatkan rekomendasi saham dengan data backtest lengkap dan tingkat akurasi yang tinggi.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-emerald-600 dark:text-emerald-400">3</div>
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">Monitor</h3>
                <p class="text-zinc-600 dark:text-zinc-400">Lakukan monitor pergerakan harga saham dengan tools yang disediakan.</p>
            </div>
        </div>
    </div>
    </section>

{{-- Interface / Features preview --}}
    <section id="fitur" class="py-16 lg:py-24 bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white mb-4">Interface Yang Mudah & Powerful</h2>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                Lihat tampilan aplikasi yang user-friendly dan dilengkapi data lengkap
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="h-48 bg-linear-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 flex items-center justify-center">
                    <svg class="w-20 h-20 text-emerald-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Dashboard Utama</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Interface yang clean untuk monitoring portfolio Anda.</p>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="h-48 bg-linear-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 flex items-center justify-center">
                    <svg class="w-20 h-20 text-emerald-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Analisis Mendalam</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Tools analisis kuantitatif dengan berbagai indikator dan metode.</p>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="h-48 bg-linear-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 flex items-center justify-center">
                    <svg class="w-20 h-20 text-emerald-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Signal & Rekomendasi</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Rekomendasi real-time berdasarkan analisis data historis dan pattern.</p>
                </div>
            </div>
        </div>
    </div>
    </section>

{{-- Testimonials --}}
    <section id="testimoni" class="py-16 lg:py-24 bg-white dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white mb-4">Apa Kata Pengguna Kami</h2>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                Testimonial dari pengguna yang telah merasakan manfaat platform kami
            </p>
        </div>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-8 border border-zinc-200 dark:border-zinc-700">
                <p class="text-zinc-700 dark:text-zinc-300 italic mb-6">"Dengan metode analisis yang tepat saya dapat profit konsisten. Alhamdulillah bisa kasih uang belanja istri untuk setahun penuh!"</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-300 font-semibold">I</div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">Iskandar</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Salatiga</p>
                    </div>
                </div>
            </div>
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-8 border border-zinc-200 dark:border-zinc-700">
                <p class="text-zinc-700 dark:text-zinc-300 italic mb-6">"Sebagai pemula, platform ini sangat membantu. 2 minggu pertama profit 500rb, akhir bulan total gain 2.5 juta. Sekarang sudah lewat UMR!"</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-300 font-semibold">Y</div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">Yulia</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Jakarta</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>

{{-- Features grid --}}
    <section class="py-16 lg:py-24 bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white mb-4">Fitur & Tools Lengkap</h2>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                Berbagai tools dan fitur untuk mendukung analisis trading Anda
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="flex gap-4 p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Quantitative Analysis</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Analisis mendalam dengan berbagai indikator trading.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Risk Management</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Tools untuk mengelola risiko dan mengoptimalkan return investasi.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Market Scanner</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Scan pasar untuk menemukan peluang trading terbaik setiap hari.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Backtesting</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Test strategi trading dengan data historis untuk validasi performa.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Performance Analytics</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Report detail kinerja strategi atau portofolio dengan metrik profitabilitas.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Blogs</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Artikel dan insight terbaru seputar trading dan investasi.</p>
                </div>
            </div>
        </div>
    </div>
    </section>

{{-- CTA --}}
    <section class="py-16 lg:py-24 bg-linear-to-br from-emerald-600 via-emerald-700 to-teal-800 dark:from-emerald-800 dark:via-emerald-900 dark:to-teal-950 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold mb-4">Siap Meningkatkan Performa Trading Anda?</h2>
        <p class="text-xl text-emerald-50/95 mb-8">
            Bergabunglah dengan ribuan trader yang telah merasakan manfaat analisis kuantitatif. Mulai hari ini!
        </p>
        @auth
            <a href="{{ url('/dashboard') }}" wire:navigate class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-white text-emerald-700 font-semibold hover:bg-emerald-50 transition shadow-lg">Buka Dashboard</a>
        @else
            <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-white text-emerald-700 font-semibold hover:bg-emerald-50 transition shadow-lg">Daftar Sekarang</a>
        @endauth
    </div>
    </section>
</div>
