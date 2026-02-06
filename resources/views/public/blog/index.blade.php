<div>
    <section class="py-12 lg:py-16 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:underline mb-4">← Kembali ke Beranda</a>
                <h1 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white">Blogs</h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-2">Artikel dan insight terbaru seputar trading dan investasi.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
                {{-- Sidebar Filter --}}
                <aside class="lg:w-64 shrink-0">
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sticky top-6 space-y-8">
                        @if($selectedCategory || !empty($selectedTags))
                            <button type="button" wire:click="clearFilters" class="w-full text-sm text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
                                Hapus filter
                            </button>
                        @endif

                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-3">Category</h3>
                            <ul class="space-y-1">
                                <li>
                                    <button type="button" wire:click="selectCategory(null)" class="w-full text-left px-3 py-2 rounded-lg text-sm transition {{ !$selectedCategory ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                                        Semua
                                    </button>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <button type="button" wire:click="selectCategory({{ json_encode($category->slug) }})" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center justify-between {{ $selectedCategory === $category->slug ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                                            <span>{{ $category->name }}</span>
                                            <span class="text-xs text-zinc-400 dark:text-zinc-500">({{ $category->posts_count }})</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-3">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tags as $tag)
                                    <button type="button" wire:click="toggleTag({{ json_encode($tag->slug) }})" class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ in_array($tag->slug, $selectedTags) ? 'bg-emerald-600 text-white dark:bg-emerald-500' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' }}">
                                        {{ $tag->name }}
                                        <span class="opacity-75">({{ $tag->posts_count }})</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Main content --}}
                <div class="flex-1 min-w-0">
                    @if($posts->isEmpty())
                        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                            <p class="text-zinc-600 dark:text-zinc-400">Tidak ada artikel yang sesuai dengan filter.</p>
                            <button type="button" wire:click="clearFilters" class="mt-4 text-emerald-600 dark:text-emerald-400 hover:underline text-sm font-medium">Hapus filter</button>
                        </div>
                    @else
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-8">
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
            </div>
        </div>
    </section>
</div>
