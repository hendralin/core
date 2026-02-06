@props(['comment', 'depth' => 0])

@php
    $marginClass = $depth > 0 ? 'ml-6 lg:ml-8 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700' : '';
@endphp
<div class="{{ $marginClass }}" wire:key="comment-{{ $comment->id }}">
    <div class="flex gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="font-medium text-zinc-900 dark:text-white">{{ $comment->user?->name ?? 'Anonim' }}</span>
                <span class="text-zinc-500 dark:text-zinc-400">·</span>
                <time class="text-zinc-500 dark:text-zinc-400" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
            </div>
            <p class="mt-1 text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">{{ $comment->content }}</p>
            @auth
                <button
                    type="button"
                    wire:click="setReplyingTo({{ $comment->id }})"
                    class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:underline"
                >
                    Balas
                </button>
            @endauth
        </div>
    </div>

    @auth
        @if($replyingToId === $comment->id)
            <form wire:submit="addReply" class="mt-4">
                <flux:textarea wire:model="commentContent" label="Tulis balasan" rows="2" placeholder="Tulis balasan..." />
                <div class="mt-2 flex gap-2">
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition">
                        Kirim Balasan
                    </button>
                    <button type="button" wire:click="cancelReply" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 text-sm font-medium transition">
                        Batal
                    </button>
                </div>
            </form>
        @endif
    @endauth

    @if($comment->approvedReplies->isNotEmpty())
        <div class="mt-4 space-y-4">
            @foreach($comment->approvedReplies as $reply)
                @include('public.blog._comment', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
