@php
    $manualInclude = $manualInclude ?? 'manual.admin-html';
    $variant = $variant ?? 'user';
    $isAdmin = $variant === 'admin';
    $subtitle = $subtitle ?? '';

    $c = [
        'hero' => $isAdmin
            ? 'from-violet-600 via-violet-700 to-indigo-950'
            : 'from-emerald-600 via-teal-700 to-emerald-950',
        'glow' => $isAdmin ? 'bg-violet-400/30' : 'bg-emerald-400/30',
        'ring' => $isAdmin ? 'ring-violet-500/30' : 'ring-emerald-500/30',
        'tocBar' => $isAdmin
            ? 'from-violet-500 to-indigo-600'
            : 'from-emerald-500 to-teal-600',
        'tocLink' => $isAdmin
            ? 'hover:bg-violet-50 dark:hover:bg-violet-950/50 focus-visible:ring-violet-500/40'
            : 'hover:bg-emerald-50 dark:hover:bg-emerald-950/50 focus-visible:ring-emerald-500/40',
        'tocDot' => $isAdmin ? 'bg-violet-500 dark:bg-violet-400' : 'bg-emerald-500 dark:bg-emerald-400',
        'chip' => $isAdmin
            ? 'border-violet-200/60 bg-violet-50/90 text-violet-900 dark:border-violet-500/30 dark:bg-violet-950/50 dark:text-violet-100'
            : 'border-emerald-200/60 bg-emerald-50/90 text-emerald-900 dark:border-emerald-500/30 dark:bg-emerald-950/50 dark:text-emerald-100',
        'articleBar' => $isAdmin
            ? 'from-violet-500 via-indigo-500 to-violet-600'
            : 'from-emerald-500 via-teal-500 to-emerald-600',
        'proseA' => $isAdmin
            ? 'prose-a:text-violet-600 dark:prose-a:text-violet-400 dark:hover:prose-a:text-violet-300'
            : 'prose-a:text-emerald-600 dark:prose-a:text-emerald-400 dark:hover:prose-a:text-emerald-300',
    ];
@endphp

<div
    class="manual-page-root -mx-4 max-w-[100vw] px-4 pb-16 sm:mx-0 sm:max-w-none sm:px-0"
    x-data="{
        tocIds: @js(collect($toc)->pluck('id')->values()->all()),
        activeId: null,
        init() {
            if (!this.tocIds.length) return;
            const obs = new IntersectionObserver(
                (entries) => {
                    entries.forEach((e) => {
                        if (e.isIntersecting) this.activeId = e.target.id;
                    });
                },
                { rootMargin: '-20% 0px -55% 0px', threshold: [0, 0.25, 0.5, 1] }
            );
            this.tocIds.forEach((id) => {
                const el = document.getElementById(id);
                if (el) obs.observe(el);
            });
        },
    }"
>
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400" aria-label="Breadcrumb">
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            class="inline-flex items-center gap-1 font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
        >
            <flux:icon.home class="size-4 shrink-0 opacity-70" />
            {{ __('Dashboard') }}
        </a>
        <span class="text-zinc-300 dark:text-zinc-600" aria-hidden="true">/</span>
        <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ __('Manual') }}</span>
    </nav>

    {{-- Hero --}}
    <header
        class="relative mb-10 overflow-hidden rounded-3xl bg-gradient-to-br {{ $c['hero'] }} px-6 py-12 shadow-2xl shadow-zinc-900/20 ring-1 ring-white/10 sm:px-10 sm:py-10"
    >
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.35]"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"
        ></div>
        <div class="pointer-events-none absolute -right-24 -top-24 size-96 rounded-full {{ $c['glow'] }} blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 size-72 rounded-full bg-black/20 blur-3xl"></div>

        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-white/90 backdrop-blur-sm">
                    @if ($isAdmin)
                        <flux:icon.shield-check class="size-3.5" />
                        {{ __('Administrator') }}
                    @else
                        <flux:icon.user class="size-3.5" />
                        {{ __('Pengguna') }}
                    @endif
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-[2.35rem] lg:leading-tight">
                    {{ $title }}
                </h1>
                @if ($subtitle !== '')
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            <div
                class="hidden md:flex shrink-0 items-center justify-center rounded-2xl border border-white/20 bg-white/10 p-6 backdrop-blur-md lg:size-36"
                aria-hidden="true"
            >
                @if ($isAdmin)
                    <flux:icon.server-stack class="size-16 text-white/90 sm:size-20" />
                @else
                    <flux:icon.book-open class="size-16 text-white/90 sm:size-20" />
                @endif
            </div>
        </div>
    </header>

    <div class="lg:grid lg:grid-cols-12 lg:items-start lg:gap-10 xl:gap-12">
        {{-- TOC --}}
        <aside class="mb-10 lg:col-span-4 xl:col-span-3">
            <div
                class="overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-xl shadow-zinc-900/5 ring-1 ring-zinc-950/5 dark:border-zinc-700/90 dark:bg-zinc-900 dark:shadow-none dark:ring-white/5"
            >
                <div class="bg-gradient-to-r {{ $c['tocBar'] }} px-5 py-4">
                    <div class="flex items-center gap-2 text-white">
                        <flux:icon.queue-list class="size-5 shrink-0 opacity-95" />
                        <span class="text-sm font-semibold tracking-tight">{{ __('Daftar isi') }}</span>
                    </div>
                    <p class="mt-1 text-xs font-medium text-white/80">{{ __('Klik untuk melompat ke bagian') }}</p>
                </div>

                <div class="p-4">
                    <label class="sr-only" for="manual-toc-select">{{ __('Pilih bagian') }}</label>
                    <select
                        id="manual-toc-select"
                        class="mb-4 w-full rounded-xl border border-zinc-200 bg-zinc-50 py-3 pl-3 pr-10 text-sm font-medium text-zinc-900 shadow-inner focus:border-transparent focus:outline-none focus:ring-2 focus:ring-offset-0 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 lg:hidden {{ $isAdmin ? 'focus:ring-violet-500/50' : 'focus:ring-emerald-500/50' }}"
                        onchange="const id = this.value; if (id) document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                    >
                        <option value="">{{ __('— Pilih bagian —') }}</option>
                        @foreach ($toc as $item)
                            <option value="{{ $item['id'] }}">
                                {{ str_repeat('· ', max(0, $item['level'] - 2)) }}{{ $item['text'] }}
                            </option>
                        @endforeach
                    </select>

                    <nav class="hidden max-h-[min(70vh,32rem)] overflow-y-auto overscroll-contain pr-1 lg:block" aria-label="{{ __('Daftar isi dokumen') }}">
                        <ul class="space-y-0.5">
                            @foreach ($toc as $item)
                                <li class="{{ $item['level'] >= 3 ? 'pl-3' : '' }} {{ $item['level'] >= 4 ? 'pl-4' : '' }}">
                                    <a
                                        href="#{{ $item['id'] }}"
                                        class="group flex items-start gap-2 rounded-xl py-2 pl-2 pr-2 text-left text-sm leading-snug outline-none transition {{ $c['tocLink'] }} focus-visible:ring-2"
                                        :class="{
                                            '{{ $isAdmin ? 'bg-violet-50 font-semibold text-violet-900 dark:bg-violet-950/60 dark:text-violet-100' : 'bg-emerald-50 font-semibold text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-100' }}':
                                                activeId === '{{ $item['id'] }}',
                                        }"
                                    >
                                        <span
                                            class="mt-1.5 size-1.5 shrink-0 rounded-full opacity-40 ring-2 ring-white/0 transition group-hover:opacity-100 {{ $c['tocDot'] }}"
                                            :class="{
                                                'opacity-100 ring-white/30': activeId === '{{ $item['id'] }}',
                                            }"
                                        ></span>
                                        <span
                                            class="flex-1 text-zinc-600 group-hover:text-zinc-900 dark:text-zinc-400 dark:group-hover:text-zinc-100"
                                            :class="{
                                                '{{ $isAdmin ? '!text-violet-900 dark:!text-violet-100' : '!text-emerald-900 dark:!text-emerald-100' }}':
                                                    activeId === '{{ $item['id'] }}',
                                            }"
                                        >
                                            {{ $item['text'] }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    @if (count($toc) === 0)
                        <p class="py-2 text-center text-xs text-zinc-500 dark:text-zinc-400">{{ __('Belum ada bagian.') }}</p>
                    @endif
                </div>
            </div>

            <div
                class="mt-4 hidden rounded-2xl border border-dashed border-zinc-300/90 bg-gradient-to-br from-zinc-50 to-white p-4 text-xs leading-relaxed text-zinc-600 dark:border-zinc-600 dark:from-zinc-900/80 dark:to-zinc-900 dark:text-zinc-400 lg:block"
            >
                <div class="flex gap-2">
                    <flux:icon.light-bulb class="size-5 shrink-0 text-amber-500 dark:text-amber-400" />
                    <p>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Tip') }}:</span>
                        {{ __('Gunakan menu di atas di ponsel. Di desktop, bagian aktif disorot saat Anda menggulir.') }}
                    </p>
                </div>
            </div>
        </aside>

        {{-- Article --}}
        <div class="min-w-0 lg:col-span-8 xl:col-span-9">
            <div
                class="relative overflow-hidden rounded-3xl border border-zinc-200/90 bg-white shadow-2xl shadow-zinc-900/[0.06] ring-1 ring-zinc-950/[0.04] dark:border-zinc-700 dark:bg-zinc-900 dark:ring-white/[0.06]"
            >
                <div class="pointer-events-none absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r {{ $c['articleBar'] }}"></div>
                <div class="pointer-events-none absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent dark:via-white/10"></div>

                <article
                    x-ref="manualArticle"
                    @class([
                        'manual-doc-content manual-article-body prose prose-zinc max-w-none px-5 py-8 sm:px-8 sm:py-10 lg:prose-lg',
                        'max-h-[min(68vh,calc(100dvh-15rem))] overflow-y-auto overscroll-y-contain lg:max-h-[min(78vh,calc(100vh-11rem))]',
                        'prose-headings:scroll-mt-28 prose-p:leading-relaxed',
                        'prose-a:font-medium prose-a:underline-offset-2 prose-a:transition-colors',
                        'hover:prose-a:text-zinc-900 dark:prose-headings:text-zinc-50',
                        'prose-blockquote:border-l-4 prose-blockquote:font-normal prose-blockquote:not-italic',
                        'prose-code:rounded-md prose-code:bg-zinc-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:before:content-none prose-code:after:content-none',
                        'prose-pre:rounded-2xl prose-pre:border prose-pre:border-zinc-200 prose-pre:bg-zinc-50 prose-pre:shadow-inner',
                        'dark:prose-invert dark:prose-code:bg-zinc-800 dark:prose-pre:border-zinc-700 dark:prose-pre:bg-zinc-950',
                        $c['proseA'],
                    ])
                >
                    @include($manualInclude)
                </article>

                <div
                    class="flex flex-col gap-3 border-t border-zinc-100 bg-gradient-to-r from-zinc-50/95 to-white px-6 py-5 dark:border-zinc-800 dark:from-zinc-950/80 dark:to-zinc-900 sm:flex-row sm:items-center sm:justify-end sm:px-10"
                >
                    <button
                        type="button"
                        class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                        @click="($refs.manualArticle?.scrollTo({ top: 0, behavior: 'smooth' }), window.scrollTo({ top: 0, behavior: 'smooth' }))"
                    >
                        <flux:icon.arrow-up class="size-3.5" />
                        {{ __('Kembali ke atas') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .manual-doc-content {
            --tw-prose-bullets: var(--tw-prose-body, #52525b);
        }
        .manual-article-body {
            scrollbar-width: thin;
            scrollbar-color: rgb(16 185 129 / 0.45) transparent;
        }
        .dark .manual-article-body {
            scrollbar-color: rgb(52 211 153 / 0.35) transparent;
        }
        .manual-article-body::-webkit-scrollbar {
            width: 8px;
        }
        .manual-article-body::-webkit-scrollbar-thumb {
            border-radius: 9999px;
            background: rgb(16 185 129 / 0.35);
        }
        .dark .manual-article-body::-webkit-scrollbar-thumb {
            background: rgb(52 211 153 / 0.3);
        }
        .manual-doc-content h2 {
            margin-top: 1.75rem;
            padding-bottom: 0.35rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .manual-doc-content h2:first-child {
            margin-top: 0;
        }
        .dark .manual-doc-content h2 {
            border-bottom-color: rgb(63 63 70 / 0.85);
        }
        .manual-doc-content h3 {
            margin-top: 1.35rem;
            font-weight: 650;
            letter-spacing: -0.02em;
        }
        .manual-doc-content .not-prose h3 {
            margin-top: 0;
            margin-bottom: 0;
        }
        .manual-doc-content hr {
            margin: 1.5rem 0;
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgb(212 212 216), transparent);
        }
        .dark .manual-doc-content hr {
            background: linear-gradient(90deg, transparent, rgb(63 63 70), transparent);
        }
        /* Tabel prose: blok + scroll (display:block merusak layout jika dipakai untuk tabel .not-prose) */
        .manual-doc-content table {
            display: block;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            border-radius: 0.875rem;
            border: 1px solid rgb(228 228 231);
            font-size: 0.9em;
        }
        .dark .manual-doc-content table {
            border-color: rgb(63 63 70);
        }
        .manual-doc-content thead {
            background: rgb(244 244 245 / 0.85);
        }
        .dark .manual-doc-content thead {
            background: rgb(39 39 42 / 0.65);
        }
        .manual-doc-content th,
        .manual-doc-content td {
            padding: 0.65rem 1rem;
            border-bottom: 1px solid rgb(228 228 231 / 0.9);
            text-align: left;
        }
        .dark .manual-doc-content th,
        .dark .manual-doc-content td {
            border-bottom-color: rgb(63 63 70 / 0.8);
        }
        .manual-doc-content tbody tr:last-child td {
            border-bottom: none;
        }
        .manual-doc-content tbody tr:hover {
            background: rgb(244 244 245 / 0.45);
        }
        .dark .manual-doc-content tbody tr:hover {
            background: rgb(39 39 42 / 0.4);
        }
        /* Tabel di .not-prose: pakai layout tabel asli + header gradien tidak tertimpa abu-abu */
        .manual-doc-content .not-prose table {
            display: table;
            overflow: visible;
            table-layout: auto;
            border-collapse: collapse;
            font-size: inherit;
        }
        .manual-doc-content .not-prose thead {
            background: transparent;
        }
        .manual-doc-content .not-prose th,
        .manual-doc-content .not-prose td {
            vertical-align: top;
        }
        .manual-doc-content ul > li::marker {
            color: rgb(113 113 122);
        }
        .manual-doc-content blockquote {
            border-radius: 0.75rem;
            background: rgb(244 244 245 / 0.65);
            padding: 0.875rem 1rem;
        }
        .dark .manual-doc-content blockquote {
            background: rgb(39 39 42 / 0.65);
        }
    </style>
</div>
