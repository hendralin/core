@assets
<script src="//cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endassets

<div class="flex flex-col max-h-[32rem] bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800"
    x-data="{ query: @entangle('input').defer, pendingMsg: null, streaming: false }">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                Fundamental Analyst (AI)
            </p>
            <p class="text-xs text-gray-500 dark:text-zinc-400">
                Contoh: "Bandingkan saham perbankan dengan ROE &gt; 15 dan DER &lt; 1 dalam 3 tahun terakhir".
            </p>
        </div>
        <flux:modal.trigger name="clear-analyst-history">
            <flux:tooltip content="Hapus riwayat chat">
                <button
                    class="text-xs text-gray-400 hover:text-red-500 dark:text-zinc-500 dark:hover:text-red-400 transition-colors"
                    title="Hapus Riwayat"
                >
                    <flux:icon.trash class="size-4" />
                </button>
            </flux:tooltip>
        </flux:modal.trigger>
    </div>

    <flux:modal name="clear-analyst-history" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus riwayat chat?</flux:heading>
                <flux:text class="mt-2">
                    Seluruh riwayat percakapan dengan analis fundamental akan dihapus.<br>
                    Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="clearHistory">Hapus Riwayat</flux:button>
            </div>
        </div>
    </flux:modal>

    <div class="flex-1 min-h-0 overflow-y-auto px-4 py-3 space-y-3 text-sm" x-ref="chatScroll">
        @foreach($messages as $message)
            <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $loop->index }}">
                @if($message['role'] === 'user')
                    <div class="max-w-[80%] rounded-lg px-3 py-2 bg-emerald-600 text-white">
                        <p class="whitespace-pre-line">{{ $message['content'] }}</p>
                    </div>
                @else
                    <div class="max-w-[80%] rounded-lg px-3 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100">
                        <div class="chat-prose">
                            {!! Str::markdown($message['content']) !!}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Optimistic user message --}}
        <div wire:loading.flex wire:target="ask" x-show="pendingMsg" class="justify-end">
            <div class="max-w-[80%] rounded-lg px-3 py-2 bg-emerald-600 text-white">
                <p class="whitespace-pre-line" x-text="pendingMsg"></p>
            </div>
        </div>

        {{-- Loading indicator (hidden once streaming starts) --}}
        <div wire:loading wire:target="ask">
            <div x-show="!streaming" class="flex justify-start">
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-zinc-800 text-xs text-gray-500 dark:text-zinc-400">
                    <flux:icon.loading class="text-green-600 size-4" /> <span>Analist sedang menyiapkan ringkasan...</span>
                </div>
            </div>
        </div>

        {{-- Streaming response --}}
        <div wire:loading wire:target="ask" class="flex justify-start">
            <div class="max-w-[80%] rounded-lg px-3 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100">
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

    <div class="border-t border-gray-200 dark:border-zinc-800 px-3 py-2">
        <form wire:submit.prevent="ask" class="flex items-end gap-2"
            @submit="
                if (query && query.trim().length >= 20) {
                    pendingMsg = query.trim();
                    query = '';
                    streaming = false;
                    $nextTick(() => { $refs.chatScroll.scrollTop = $refs.chatScroll.scrollHeight; });
                }
            ">
            <textarea
                x-model="query"
                wire:model.defer="input"
                rows="2"
                class="flex-1 resize-none pl-2 pr-2 pt-0.5 text-sm rounded-md border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-500 dark:focus:border-emerald-500"
                placeholder="Tulis pertanyaan analisa fundamental di sini..."></textarea>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-3 py-2 disabled:opacity-60"
                wire:loading.attr="disabled"
                wire:target="ask"
                :disabled="!query || query.trim().length < 20"
            >
                Kirim
            </button>
        </form>
    </div>
</div>
