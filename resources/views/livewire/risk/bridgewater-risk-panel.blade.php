@assets
<script src="//cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endassets

<div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('AI Risk Assessment') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400 mb-4">
            {{ __('Bridgewater-style risk report: korelasi, konsentrasi sektor, likuiditas, stress test, tail risk, dan rebalancing. Angka kuantitatif dari tool; bukan saran investasi.') }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Portofolio') }}</h2>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label for="bridgewater-portfolio-text">{{ __('Holdings (satu baris per emiten)') }}</flux:label>
                        <flux:textarea
                            id="bridgewater-portfolio-text"
                            wire:model="portfolio_text"
                            rows="10"
                            class="min-h-[12rem] w-full"
                            placeholder="{{ __('BBCA 25') }}"
                        ></flux:textarea>
                        <flux:error name="portfolio_text" />
                        <flux:description>{{ __('Satu emiten per baris: kode lalu persen (mis. BBCA 25).') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Total nilai portofolio (IDR, opsional)') }}</flux:label>
                        <flux:input wire:model="total_portfolio_value_idr" placeholder="100000000" />
                        <flux:error name="total_portfolio_value_idr" />
                    </flux:field>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <flux:button variant="primary" wire:click="generate" wire:loading.attr="disabled" wire:target="generate">
                            {{ __('Generate report') }}
                        </flux:button>
                        <flux:modal.trigger name="clear-bridgewater-risk-history">
                            <flux:button variant="ghost" type="button">{{ __('Hapus riwayat') }}</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-gray-300 dark:border-zinc-600 p-3 text-xs text-gray-600 dark:text-zinc-400">
                <p class="font-medium text-gray-800 dark:text-zinc-200">{{ __('Data hybrid') }}</p>
                <p class="mt-1">{{ __('FINNHUB_API_KEY memperkaya kutipan/metrik. Korelasi dan likuiditas utama dari data internal IDX.') }}</p>
            </div>
        </div>

        <div class="lg:col-span-8 flex min-h-0">
            <div class="flex flex-col w-full h-[min(96rem,calc(100vh-8rem))] overflow-hidden bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl shadow-sm"
                x-data="{ streaming: false, copyReport() {
                    const el = document.getElementById('bridgewater-risk-report-md');
                    if (!el) return;
                    navigator.clipboard.writeText(el.innerText);
                } }">

                <div class="shrink-0 px-4 py-3 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Risk report') }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">{{ __('Heat map & analisis') }}</p>
                    </div>
                    <flux:button size="sm" variant="ghost" type="button" x-on:click="copyReport()">{{ __('Salin teks') }}</flux:button>
                </div>

                <flux:modal name="clear-bridgewater-risk-history" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Hapus riwayat laporan?') }}</flux:heading>
                            <flux:text class="mt-2">
                                {{ __('Semua laporan AI Risk Assessment untuk akun ini akan dihapus.') }}
                            </flux:text>
                        </div>
                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                            </flux:modal.close>
                            <flux:button variant="danger" wire:click="clearHistory">{{ __('Hapus') }}</flux:button>
                        </div>
                    </div>
                </flux:modal>

                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-4 py-3 space-y-3 text-sm" x-ref="chatScroll" id="bridgewater-risk-report-md">
                    @foreach($messages as $message)
                        @php($content = is_string($message['content'] ?? null) ? trim($message['content']) : '')
                        @continue($content === '' || $content === '[]')
                        <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $loop->index }}">
                            @if($message['role'] === 'user')
                                <div class="max-w-[90%] rounded-lg px-3 py-2 bg-emerald-600 text-white">
                                    <p class="whitespace-pre-line">{{ $message['content'] }}</p>
                                </div>
                            @else
                                <div class="max-w-[90%] rounded-lg px-3 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100">
                                    <div class="chat-prose">
                                        {!! Str::markdown($message['content']) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div wire:loading wire:target="generate">
                        <div x-show="!streaming" class="flex justify-start">
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-zinc-800 text-xs text-gray-500 dark:text-zinc-400">
                                <flux:icon.loading class="text-green-600 size-4" />
                                <span>{{ __('Menyusun laporan risiko…') }}</span>
                            </div>
                        </div>
                    </div>

                    <div wire:loading wire:target="generate" class="flex justify-start">
                        <div class="max-w-[90%] rounded-lg px-3 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100">
                            <div class="chat-prose" wire:stream="streamResponse"
                                x-init="
                                    new MutationObserver(() => {
                                        if ($el.textContent.trim()) streaming = true;
                                        let c = $el.closest('.overflow-y-auto');
                                        if (c) c.scrollTop = c.scrollHeight;
                                    }).observe($el, { childList: true, characterData: true, subtree: true });
                                "
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
