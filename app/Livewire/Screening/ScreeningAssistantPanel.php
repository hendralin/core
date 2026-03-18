<?php

namespace App\Livewire\Screening;

use App\Neuron\Agents\ScreeningAgent;
use Livewire\Attributes\Locked;
use Livewire\Component;
use NeuronAI\Chat\Messages\UserMessage;

class ScreeningAssistantPanel extends Component
{
    public string $input = '';

    /**
     * Simple chat log for the session.
     *
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
                'content' => 'Halo, saya asisten screening Bandar Saham. Jelaskan gaya trading atau filter yang kamu inginkan, misalnya: "screening saham bullish dengan volume besar hari ini".',
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
            $agent = ScreeningAgent::make();

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
                'content' => 'Maaf, terjadi kesalahan saat menjalankan asisten screening: ' . $e->getMessage(),
            ];
        } finally {
            $this->isThinking = false;
        }
    }

    public function render()
    {
        return view('livewire.screening.screening-assistant-panel');
    }
}

