<?php

namespace App\Livewire\Analysis;

use App\Neuron\Agents\SqlAnalystAgent;
use Livewire\Attributes\Locked;
use Livewire\Component;
use NeuronAI\Chat\Messages\UserMessage;

class SqlAnalystPanel extends Component
{
    public string $input = '';

    /**
     * @var array<int, array{role:string, content:string}>
     */
    #[Locked]
    public array $messages = [];

    public bool $isThinking = false;

    public function mount(): void
    {
        if ($this->messages === []) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Halo, saya analis fundamental Bandar Saham. Tanyakan perbandingan rasio (PER, PBV, ROE, DER, dsb.) untuk sektor atau emiten tertentu.',
            ];
        }
    }

    public function ask(): void
    {
        $content = trim($this->input);
        if ($content === '' || $this->isThinking) {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'content' => $content,
        ];
        $this->input = '';
        $this->isThinking = true;

        try {
            $agent = SqlAnalystAgent::make();

            $response = $agent
                ->chat(new UserMessage($content))
                ->getMessage()
                ->getContent();

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];
        } catch (\Throwable $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Maaf, terjadi kesalahan saat menjalankan analis SQL: ' . $e->getMessage(),
            ];
        } finally {
            $this->isThinking = false;
        }
    }

    public function render()
    {
        return view('livewire.analysis.sql-analyst-panel');
    }
}

