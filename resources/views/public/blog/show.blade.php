<div>
    <article class="py-12 lg:py-16 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('blogs.index') }}" wire:navigate class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:underline mb-8">← Kembali ke Blogs</a>

            <header class="mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold text-zinc-900 dark:text-white leading-tight">{{ $post->title }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-4 text-sm text-zinc-600 dark:text-zinc-400">
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('d F Y') }}</time>
                    @if($post->user)
                        <span>·</span>
                        <span>{{ $post->user->name }}</span>
                    @endif
                </div>
            </header>

            @if($post->featured_image_url)
                <div class="rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 mb-8">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full aspect-video object-cover">
                </div>
            @endif

            @if($post->excerpt)
                <p class="text-lg text-zinc-600 dark:text-zinc-400 border-l-4 border-emerald-500 pl-4 mb-8">{{ $post->excerpt }}</p>
            @endif

            <div class="prose prose-zinc dark:prose-invert max-w-none prose-headings:text-zinc-900 dark:prose-headings:text-white prose-p:text-zinc-700 dark:prose-p:text-zinc-300 prose-a:text-emerald-600 dark:prose-a:text-emerald-400">
                {!! $post->content !!}
            </div>

            @if($post->categories->isNotEmpty() || $post->tags->isNotEmpty())
                <footer class="mt-10 pt-8 border-t border-zinc-200 dark:border-zinc-800">
                    @if($post->categories->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Kategori:</span>
                            @foreach($post->categories as $category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">{{ $category->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($post->tags->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tag:</span>
                            @foreach($post->tags as $tag)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </footer>
            @endif
        </div>
    </article>
</div>
