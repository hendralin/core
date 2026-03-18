<?php

namespace App\Livewire\Screening;

use App\Models\ChatMessage;
use App\Neuron\Agents\ScreeningAgent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;
use NeuronAI\Chat\Messages\UserMessage;

class ScreeningAssistantPanel extends Component
{
    public string $input = '';

    private string $greeting = 'Halo, saya asisten screening Bandar Saham. Jelaskan gaya trading atau filter yang kamu inginkan, misalnya: "screening saham bullish dengan volume besar hari ini".';

    protected function threadId(): string
    {
        return 'screening-' . Auth::id();
    }

    public function ask(): void
    {
        $content = trim($this->input);
        if ($content === '') {
            return;
        }

        $this->input = '';

        try {
            $handler = ScreeningAgent::make()
                ->withThread($this->threadId())
                ->stream(new UserMessage($content));

            foreach ($handler->events() as $chunk) {
                if ($chunk instanceof TextChunk) {
                    $this->stream(content: $chunk->content, name: 'streamResponse');
                }
            }

            $handler->getMessage();
        } catch (\Throwable $e) {
            ChatMessage::create([
                'thread_id' => $this->threadId(),
                'role' => 'assistant',
                'content' => json_encode([['type' => 'text', 'text' => 'Maaf, terjadi kesalahan saat menjalankan asisten screening: ' . $e->getMessage()]]),
            ]);
        }
    }

    public function clearHistory(): void
    {
        ChatMessage::where('thread_id', $this->threadId())->delete();
        $this->modal('clear-screening-history')->close();
    }

    public function render()
    {
        $messages = collect([
            ['role' => 'assistant', 'content' => $this->greeting],
        ]);

        $history = ChatMessage::where('thread_id', $this->threadId())
            ->orderBy('id')
            ->get()
            ->map(fn (ChatMessage $msg) => [
                'role' => $msg->role,
                'content' => $this->extractText($msg->content),
            ]);

        $messages = $messages->concat($history);

        return view('livewire.screening.screening-assistant-panel', [
            'messages' => $messages,
        ]);
    }

    private function extractText(mixed $content): string
    {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $content = $decoded;
            } else {
                return $content;
            }
        }

        if (is_array($content)) {
            $texts = [];
            foreach ($content as $block) {
                if (is_array($block) && ($block['type'] ?? '') === 'text') {
                    $texts[] = $block['content'] ?? $block['text'] ?? '';
                }
            }

            return implode("\n", array_filter($texts)) ?: (string) collect($content)->pluck('content')->filter()->implode("\n");
        }

        return (string) $content;
    }
}
