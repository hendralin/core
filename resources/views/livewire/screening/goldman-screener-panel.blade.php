@assets
<script src="//cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endassets

<div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('AI Screener') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400 mb-4">
            {{ __('Screening riset ekuitas untuk IDX. Angka kuantitatif dari data tool; target harga dan moat adalah penilaian dengan asumsi.') }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Profile form --}}
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Investment profile') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">{{ __('Risk tolerance') }} (1–5)</label>
                        <input type="range" min="1" max="5" wire:model.live="risk"
                            class="w-full accent-emerald-600" />
                        <div class="flex justify-between text-[10px] text-gray-500 dark:text-zinc-500">
                            <span>{{ __('Conservative') }}</span>
                            <span class="font-medium text-emerald-600">{{ $risk }}</span>
                            <span>{{ __('Aggressive') }}</span>
                        </div>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Amount (IDR, optional)') }}</flux:label>
                        <flux:input wire:model="amount" placeholder="e.g. 500000000" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Time horizon (months)') }}</flux:label>
                        <flux:input type="number" wire:model="horizon_months" min="1" max="600" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Preferred sectors') }}</flux:label>
                        <flux:textarea wire:model="sectors" rows="3"
                            placeholder="{{ __('Comma-separated, e.g. Keuangan, Teknologi') }}" />
                    </flux:field>

                    <flux:field>
                        <flux:checkbox wire:model="sharia_only" label="{{ __('Sharia only') }}" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-3">
                        <flux:field>
                            <flux:label>{{ __('Min ROE % (optional)') }}</flux:label>
                            <flux:input wire:model="min_roe" placeholder="e.g. 10" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Max PER (optional)') }}</flux:label>
                            <flux:input wire:model="max_per" placeholder="e.g. 15" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Max debt/equity (optional)') }}</flux:label>
                            <flux:input wire:model="max_de" placeholder="e.g. 1.5" />
                        </flux:field>
                    </div>

                    @error('risk')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-wrap gap-2 pt-2">
                        <flux:button variant="primary" wire:click="generate" wire:loading.attr="disabled" wire:target="generate">
                            {{ __('Generate report') }}
                        </flux:button>
                        <flux:modal.trigger name="clear-goldman-history">
                            <flux:button variant="ghost" type="button">{{ __('Clear chat') }}</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-gray-300 dark:border-zinc-600 p-3 text-xs text-gray-600 dark:text-zinc-400">
                <p class="font-medium text-gray-800 dark:text-zinc-200">{{ __('Optional data enrichment') }}</p>
                <p class="mt-1">{{ __('Set FINNHUB_API_KEY in .env for cross-check quotes/metrics (hybrid mode).') }}</p>
            </div>
        </div>

        {{-- Report / chat: fixed height; messages scroll inside (no endless page stretch) --}}
        <div class="lg:col-span-8 flex min-h-0">
            <div class="flex flex-col w-full h-[min(96rem,calc(100vh-8rem))] overflow-hidden bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl shadow-sm"
                x-data="{ streaming: false, copyReport() {
                    const el = document.getElementById('goldman-report-md');
                    if (!el) return;
                    navigator.clipboard.writeText(el.innerText);
                } }">

                <div class="shrink-0 px-4 py-3 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Screening report') }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">{{ __('Laporan screening dalam format tabel Markdown & analisis') }}</p>
                    </div>
                    <flux:button size="sm" variant="ghost" type="button" x-on:click="copyReport()">{{ __('Copy text') }}</flux:button>
                </div>

                <flux:modal name="clear-goldman-history" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Clear chat history?') }}</flux:heading>
                            <flux:text class="mt-2">
                                {{ __('All AI Screener messages for this account will be deleted.') }}
                            </flux:text>
                        </div>
                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                            </flux:modal.close>
                            <flux:button variant="danger" wire:click="clearHistory">{{ __('Clear') }}</flux:button>
                        </div>
                    </div>
                </flux:modal>

                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-4 py-3 space-y-3 text-sm" x-ref="chatScroll" id="goldman-report-md">
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
                                <span>{{ __('Building screening report…') }}</span>
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
