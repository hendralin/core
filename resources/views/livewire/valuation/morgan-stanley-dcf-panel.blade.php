@assets
<script src="//cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endassets

<div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('AI Valuation') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400 mb-4">
            {{ __('Memo valuasi DCF (proxy) untuk satu emiten IDX. FCF bukan laporan kas penuh; WACC & terminal memakai asumsi yang dijelaskan di memo.') }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Input valuasi') }}</h2>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Kode emiten') }}</flux:label>
                        <flux:input wire:model="code" placeholder="BBCA" class="uppercase" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-3">
                        <flux:field>
                            <flux:label>{{ __('Pertumbuhan pendapatan % / tahun (opsional)') }}</flux:label>
                            <flux:input wire:model="revenue_growth_annual" placeholder="8" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('NPM % (opsional)') }}</flux:label>
                            <flux:input wire:model="npm_pct" placeholder="28" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Terminal growth % (opsional)') }}</flux:label>
                            <flux:input wire:model="terminal_growth_pct" placeholder="2.5" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Beta (opsional)') }}</flux:label>
                            <flux:input wire:model="beta" placeholder="1.0" />
                        </flux:field>
                    </div>

                    @error('code')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-wrap gap-2 pt-2">
                        <flux:button variant="primary" wire:click="generate" wire:loading.attr="disabled" wire:target="generate">
                            {{ __('Generate memo') }}
                        </flux:button>
                        <flux:modal.trigger name="clear-morgan-dcf-history">
                            <flux:button variant="ghost" type="button">{{ __('Hapus riwayat') }}</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-gray-300 dark:border-zinc-600 p-3 text-xs text-gray-600 dark:text-zinc-400">
                <p class="font-medium text-gray-800 dark:text-zinc-200">{{ __('Data hybrid') }}</p>
                <p class="mt-1">{{ __('FINNHUB_API_KEY memperkaya beta/kutipan. Tanpa kunci, model memakai asumsi default.') }}</p>
            </div>
        </div>

        <div class="lg:col-span-8 flex min-h-0">
            <div class="flex flex-col w-full h-[min(96rem,calc(100vh-8rem))] overflow-hidden bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl shadow-sm"
                x-data="{ streaming: false, copyReport() {
                    const el = document.getElementById('morgan-dcf-report-md');
                    if (!el) return;
                    navigator.clipboard.writeText(el.innerText);
                } }">

                <div class="shrink-0 px-4 py-3 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Memo DCF') }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">{{ __('Tabel & matematika eksplisit') }}</p>
                    </div>
                    <flux:button size="sm" variant="ghost" type="button" x-on:click="copyReport()">{{ __('Salin teks') }}</flux:button>
                </div>

                <flux:modal name="clear-morgan-dcf-history" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Hapus riwayat memo?') }}</flux:heading>
                            <flux:text class="mt-2">
                                {{ __('Semua memo AI Valuation untuk akun ini akan dihapus.') }}
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

                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-4 py-3 space-y-3 text-sm" x-ref="chatScroll" id="morgan-dcf-report-md">
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
                                <span>{{ __('Menyusun memo…') }}</span>
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
