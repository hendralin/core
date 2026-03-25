<?php

namespace App\Livewire\Risk;

use App\Models\ChatMessage;
use App\Neuron\Agents\BridgewaterRiskAssessmentAgent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;
use NeuronAI\Chat\Messages\UserMessage;

#[Title('AI Risk Assessment')]
class BridgewaterRiskPanel extends Component
{
    public string $portfolio_text = '';

    public string $total_portfolio_value_idr = '';

    private string $greeting = 'Tempel **daftar portofolio** (satu baris per emiten: kode dan persen), isi opsional **total nilai portofolio (IDR)**, lalu klik **Generate report**. Analisis memakai data historis internal IDX dan opsional **Finnhub**. **Laporan dalam Bahasa Indonesia.**';

    protected function threadId(): string
    {
        return 'bridgewater-risk-'.Auth::id();
    }

    public function generate(): void
    {
        $this->validate([
            'portfolio_text' => 'required|string|max:8000',
            'total_portfolio_value_idr' => 'nullable|numeric|min:0',
        ]);

        $prompt = $this->buildPrompt();

        try {
            $handler = BridgewaterRiskAssessmentAgent::make()
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
                'content' => json_encode([['type' => 'text', 'text' => 'Terjadi kesalahan saat menjalankan AI Risk Assessment: '.$e->getMessage()]]),
            ]);
        }
    }

    public function clearHistory(): void
    {
        ChatMessage::where('thread_id', $this->threadId())->delete();
        $this->modal('clear-bridgewater-risk-history')->close();
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

        return view('livewire.risk.bridgewater-risk-panel', [
            'messages' => $messages,
        ])->layout('layouts.app', [
            'title' => __('AI Risk Assessment'),
        ]);
    }

    private function buildPrompt(): string
    {
        $portfolio = trim($this->portfolio_text);
        $total = trim($this->total_portfolio_value_idr);
        $totalLine = $total !== '' ? "Total nilai portofolio (IDR): {$total}" : 'Total nilai portofolio (IDR): tidak diisi';

        return <<<PROMPT
Susun laporan manajemen risiko profesional (gaya Bridgewater / radical transparency) untuk portofolio saham IDX saya.

{$totalLine}

Daftar portofolio (kode dan bobot % per baris):
---
{$portfolio}
---

WAJIB lakukan dengan tool:
1) parse_portfolio_input untuk memvalidasi dan menormalisasi bobot
2) build_portfolio_risk_snapshot (sertakan total_portfolio_value_idr jika tersedia sebagai angka)
3) run_recession_stress_test
4) propose_rebalancing_plan

Isi laporan (Bahasa Indonesia) harus mencakup:
- Ringkasan eksekutif
- Tabel heat map summary (dari heat_map_summary tool)
- Analisis korelasi antar holding
- Risiko konsentrasi sektor dengan persentase
- Eksposur geografis dan risiko mata uang (gunakan proxy dari tool; jujur jika data terbatas)
- Sensitivitas suku bunga per posisi
- Stress test resesi / estimasi drawdown
- Rating likuiditas per holding
- Risiko single-stock dan rekomendasi ukuran posisi
- Tail risk dengan perkiraan probabilitas indikatif (dengan penafian)
- Tiga risiko terbesar dan ide hedging (kualitatif; tidak mengarang angka)
- Usulan rebalancing dengan persentase alokasi spesifik dari tool

Aturan: semua angka kuantitatif dari output tool; jika Finnhub tidak aktif, sebutkan.
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
