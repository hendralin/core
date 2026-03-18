<?php

namespace App\Neuron;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;

abstract class BaseStockAgent extends Agent
{
    /**
     * Resolve the AI provider based on configuration / environment.
     */
    protected function provider(): AIProviderInterface
    {
        $provider = config('services.neuron.provider', env('NEURON_PROVIDER', 'openai'));

        return match (strtolower((string) $provider)) {
            'anthropic' => new Anthropic(
                key: (string) config('services.neuron.anthropic.key', env('ANTHROPIC_API_KEY', '')),
                model: (string) config('services.neuron.anthropic.model', env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-latest')),
            ),
            'gemini' => new Gemini(
                key: (string) config('services.neuron.gemini.key', env('GEMINI_API_KEY', '')),
                model: (string) config('services.neuron.gemini.model', env('GEMINI_MODEL', 'gemini-2.0-flash')),
            ),
            'ollama' => new Ollama(
                url: (string) config('services.neuron.ollama.url', env('OLLAMA_URL', 'http://localhost:11434')),
                model: (string) config('services.neuron.ollama.model', env('OLLAMA_MODEL', 'llama3.1')),
            ),
            default => new OpenAI(
                key: (string) config('services.neuron.openai.key', env('OPENAI_API_KEY', '')),
                model: (string) config('services.neuron.openai.model', env('OPENAI_MODEL', 'gpt-4.1-mini')),
            ),
        };
    }
}

