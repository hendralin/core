<div>
    <section class="py-12 lg:py-16 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:underline mb-4">← Kembali ke Beranda</a>
                <h1 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white">Blogs</h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-2">Artikel dan insight terbaru seputar trading dan investasi.</p>
            </div>

            @if($posts->isEmpty())
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                    <p class="text-zinc-600 dark:text-zinc-400">Belum ada artikel yang dipublikasikan.</p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <a href="{{ route('blogs.show', $post) }}" wire:navigate class="group bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition">
                            @if($post->featured_image_url)
                                <div class="aspect-video bg-zinc-200 dark:bg-zinc-800">
                                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </div>
                            @else
                                <div class="aspect-video bg-linear-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-emerald-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                            <div class="p-6">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-2">{{ $post->published_at?->format('d M Y') }}</p>
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition line-clamp-2">{{ $post->title }}</h2>
                                @if($post->excerpt)
                                    <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-2 line-clamp-2">{{ Str::limit(strip_tags($post->excerpt), 100) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
