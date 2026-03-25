<?php

namespace App\Livewire\Screening;

use App\Models\ChatMessage;
use App\Neuron\Agents\GoldmanScreenerAgent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;
use NeuronAI\Chat\Messages\UserMessage;

#[Title('AI Screener')]
class GoldmanScreenerPanel extends Component
{
    public int $risk = 3;

    public string $amount = '';

    public int $horizon_months = 12;

    public string $sectors = '';

    public bool $sharia_only = false;

    public ?string $min_roe = null;

    public ?string $max_per = null;

    public ?string $max_de = null;

    private string $greeting = 'Atur profil investasi di bawah ini, lalu klik **Generate report** untuk laporan screening bergaya riset ekuitas (IDX). Angka kuantitatif bersumber dari data internal dan opsional penyegaran Finnhub. **Laporan akan ditulis dalam Bahasa Indonesia.**';

    protected function threadId(): string
    {
        return 'goldman-screener-'.Auth::id();
    }

    public function generate(): void
    {
        $this->min_roe = $this->min_roe === '' ? null : $this->min_roe;
        $this->max_per = $this->max_per === '' ? null : $this->max_per;
        $this->max_de = $this->max_de === '' ? null : $this->max_de;

        $this->validate([
            'risk' => 'required|integer|min:1|max:5',
            'horizon_months' => 'required|integer|min:1|max:600',
            'amount' => 'nullable|string|max:50',
            'sectors' => 'nullable|string|max:500',
            'min_roe' => 'nullable|numeric',
            'max_per' => 'nullable|numeric',
            'max_de' => 'nullable|numeric',
        ]);

        $prompt = $this->buildPrompt();

        try {
            $handler = GoldmanScreenerAgent::make()
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
                'content' => json_encode([['type' => 'text', 'text' => 'Terjadi kesalahan saat menjalankan AI Screener: '.$e->getMessage()]]),
            ]);
        }
    }

    public function clearHistory(): void
    {
        ChatMessage::where('thread_id', $this->threadId())->delete();
        $this->modal('clear-goldman-history')->close();
    }

    public function render()
    {
        $messages = collect([
            ['role' => 'assistant', 'content' => $this->greeting],
        ]);

        // Jangan tampilkan bubble untuk pesan user: isinya prompt panjang (tidak perlu dilihat di UI).
        $history = ChatMessage::where('thread_id', $this->threadId())
            ->orderBy('id')
            ->get()
            ->filter(fn (ChatMessage $msg) => $msg->role !== 'user')
            ->map(fn (ChatMessage $msg) => [
                'role' => $msg->role,
                'content' => $this->extractText($msg->content),
            ]);

        $messages = $messages->concat($history);

        return view('livewire.screening.goldman-screener-panel', [
            'messages' => $messages,
        ])->layout('layouts.app', [
            'title' => __('AI Screener'),
        ]);
    }

    private function buildPrompt(): string
    {
        $riskLabel = match ($this->risk) {
            1 => 'sangat rendah',
            2 => 'rendah',
            3 => 'sedang',
            4 => 'tinggi',
            5 => 'sangat tinggi',
            default => 'sedang',
        };

        $amount = trim($this->amount) !== '' ? trim($this->amount) : 'tidak diisi';
        $sectors = trim($this->sectors) !== '' ? trim($this->sectors) : 'tanpa preferensi / semua sektor';
        $minRoe = $this->min_roe !== null && $this->min_roe !== '' ? $this->min_roe : 'tidak ada';
        $maxPer = $this->max_per !== null && $this->max_per !== '' ? $this->max_per : 'tidak ada';
        $maxDe = $this->max_de !== null && $this->max_de !== '' ? $this->max_de : 'tidak ada';

        $sharia = $this->sharia_only ? 'ya' : 'tidak';

        return <<<PROMPT
Kamu adalah analis ekuitas senior yang menyusun laporan screening saham bergaya riset institusi (referensi: kerangka Goldman-style) untuk emiten Indonesia (IDX/BEI).

Profil investasi saya:
- Toleransi risiko: {$riskLabel} (skor {$this->risk}/5)
- Nominal investasi (perkiraan, IDR): {$amount}
- Horizon waktu: {$this->horizon_months} bulan
- Sektor yang diutamakan: {$sectors}
- Hanya saham syariah: {$sharia}
- Filter opsional — minimal ROE %: {$minRoe}; maksimal PER: {$maxPer}; maksimal debt/equity: {$maxDe}

WAJIB disertakan (tulis seluruhnya dalam Bahasa Indonesia):
1) Ringkasan eksekutif
2) Top 10 saham yang selaras dengan profil — kode emiten (IDX)
3) Per nama: analisis PER dibanding rata-rata sektor (dari tool), tren pertumbuhan pendapatan dari ~5 titik laporan terakhir, kesehatan debt-to-equity, yield dividen dan skor keberlanjutan pembayaran dividen (1-10) beserta alasan, rating moat kompetitif (lemah|sedang|kuat), target harga bull & bear 12 bulan (IDR), rating risiko 1-10 beserta alasan, zona entry dan stop-loss yang disarankan (IDR)
4) Tabel ringkasan profesional dalam Markdown
5) Asumsi dan keterbatasan; sebutkan jika penyegaran Finnhub tidak tersedia

Aturan:
- Gunakan tool untuk semua data kuantitatif. Dilarang mengarang harga atau rasio.
- Jika sektor diisi, utamakan screening ke sektor tersebut lewat screen_fundamental_candidates dan sector_valuation_benchmark.
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
