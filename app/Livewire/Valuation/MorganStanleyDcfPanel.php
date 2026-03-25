<?php

namespace App\Livewire\Valuation;

use App\Models\ChatMessage;
use App\Neuron\Agents\MorganStanleyDcfAgent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;
use NeuronAI\Chat\Messages\UserMessage;

#[Title('AI Valuation')]
class MorganStanleyDcfPanel extends Component
{
    public string $code = '';

    public ?string $revenue_growth_annual = null;

    public ?string $npm_pct = null;

    public ?string $terminal_growth_pct = null;

    public ?string $beta = null;

    private string $greeting = 'Isi **kode emiten**, lalu klik **Generate memo**. Nama perusahaan diambil dari data fundamental. Model memakai **DCF proxy** (FCF dari laba × faktor konversi) dengan data internal IDX dan opsional **Finnhub** untuk beta/kutipan. **Memo dalam Bahasa Indonesia.**';

    protected function threadId(): string
    {
        return 'morgan-dcf-'.Auth::id();
    }

    public function generate(): void
    {
        $this->revenue_growth_annual = $this->revenue_growth_annual === '' ? null : $this->revenue_growth_annual;
        $this->npm_pct = $this->npm_pct === '' ? null : $this->npm_pct;
        $this->terminal_growth_pct = $this->terminal_growth_pct === '' ? null : $this->terminal_growth_pct;
        $this->beta = $this->beta === '' ? null : $this->beta;

        $this->validate([
            'code' => 'required|string|max:10',
            'revenue_growth_annual' => 'nullable|numeric',
            'npm_pct' => 'nullable|numeric',
            'terminal_growth_pct' => 'nullable|numeric',
            'beta' => 'nullable|numeric',
        ]);

        $prompt = $this->buildPrompt();

        try {
            $handler = MorganStanleyDcfAgent::make()
                ->withThread($this->threadId())
                ->stream(new UserMessage($prompt));

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
                'content' => json_encode([['type' => 'text', 'text' => 'Terjadi kesalahan saat menjalankan AI Valuation: '.$e->getMessage()]]),
            ]);
        }
    }

    public function clearHistory(): void
    {
        ChatMessage::where('thread_id', $this->threadId())->delete();
        $this->modal('clear-morgan-dcf-history')->close();
    }

    public function render()
    {
        $messages = collect([
            ['role' => 'assistant', 'content' => $this->greeting],
        ]);

        $history = ChatMessage::where('thread_id', $this->threadId())
            ->orderBy('id')
            ->get()
            ->filter(fn (ChatMessage $msg) => $msg->role !== 'user')
            ->map(fn (ChatMessage $msg) => [
                'role' => $msg->role,
                'content' => $this->extractText($msg->content),
            ]);

        $messages = $messages->concat($history);

        return view('livewire.valuation.morgan-stanley-dcf-panel', [
            'messages' => $messages,
        ])->layout('layouts.app', [
            'title' => __('AI Valuation'),
        ]);
    }

    private function buildPrompt(): string
    {
        $code = strtoupper(trim($this->code));

        $g = $this->revenue_growth_annual !== null ? (string) $this->revenue_growth_annual : 'default (dari histori CAGR terkunci asumsi)';
        $npm = $this->npm_pct !== null ? (string) $this->npm_pct : 'default (NPM laporan terakhir)';
        $tg = $this->terminal_growth_pct !== null ? (string) $this->terminal_growth_pct : 'default 2,5%';
        $b = $this->beta !== null ? (string) $this->beta : 'default (Finnhub jika ada, selain itu 1,0)';

        return <<<PROMPT
Susun memo valuasi DCF (gaya investment banking / Morgan Stanley) untuk satu saham IDX.

Emiten: {$code}

Permintaan isi memo (wajib pakai tool; bahasa Indonesia):
- Proyeksi pendapatan 5 tahun dengan asumsi pertumbuhan (override user jika ada)
- Estimasi margin operasi dari tren historis (NPM sebagai proxy; jelaskan)
- FCF tahun demi tahun (proxy dari laba × faktor konversi; jelaskan rumus)
- Estimasi WACC (jelaskan komponen Re, Rd, bobot utang/modal)
- Nilai terminal: metode perpetuity growth DAN metode exit multiple
- Tabel sensitivitas fair value vs diskonto / terminal growth
- Bandingkan nilai wajar vs harga pasar saat ini
- Verdict: undervalued / fairly valued / overvalued
- Asumsi kunci yang bisa mematahkan model

Parameter opsional dari user (persen numerik jika diisi):
- Pertumbuhan pendapatan tahunan %: {$g}
- NPM %: {$npm}
- Terminal growth %: {$tg}
- Beta: {$b}

Aturan: jangan mengarang angka di luar tool; jika Finnhub tidak aktif, sebutkan.
PROMPT;
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
