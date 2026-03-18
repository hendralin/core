@assets
<script src="//cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endassets

<div class="flex flex-col max-h-[32rem] bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800"
    x-data="{ query: @entangle('input').defer, pendingMsg: null }">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-800">
        <p class="text-sm font-semibold text-gray-900 dark:text-white">
            Fundamental Analyst (AI)
        </p>
        <p class="text-xs text-gray-500 dark:text-zinc-400">
            Contoh: "Bandingkan saham perbankan dengan ROE &gt; 15 dan DER &lt; 1 dalam 3 tahun terakhir".
        </p>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto px-4 py-3 space-y-3 text-sm" x-ref="chatScroll">
        @foreach($messages as $message)
            <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $loop->index }}">
                @if($message['role'] === 'user')
                    <div class="max-w-[80%] rounded-lg px-3 py-2 bg-emerald-600 text-white">
                        <p class="whitespace-pre-line">{{ $message['content'] }}</p>
                    </div>
                @elseif($loop->last && count($messages) > 1)
                    <div class="max-w-[80%] rounded-lg px-3 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100">
                        <div class="chat-prose"
                            x-data="{
                                text: '',
                                words: @js($message['content']).split(/(?<=\s)/),
                                i: 0,
                                type() {
                                    if (this.i < this.words.length) {
                                        this.text += this.words[this.i++];
                                        this.$el.innerHTML = marked.parse(this.text);
                                        let c = this.$el.closest('.overflow-y-auto');
                                        if (c) c.scrollTop = c.scrollHeight;
                                        setTimeout(() => this.type(), Math.max(8, Math.min(25, 3500 / this.words.length)));
                                    }
                                }
                            }"
                            x-init="type()"
                        ></div>
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

        {{-- Loading indicator --}}
        <div wire:loading wire:target="ask" class="flex justify-start">
            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-zinc-800 text-xs text-gray-500 dark:text-zinc-400">
                <flux:icon.loading class="text-green-600 size-4" /> <span>Analist sedang menyiapkan ringkasan...</span>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-zinc-800 px-3 py-2">
        <form wire:submit.prevent="ask" class="flex items-end gap-2"
            @submit="
                if (query && query.trim().length >= 20) {
                    pendingMsg = query.trim();
                    query = '';
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
