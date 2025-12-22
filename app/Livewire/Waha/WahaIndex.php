<?php

namespace App\Livewire\Waha;

use App\Models\Config;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WahaIndex extends Component
{
    public $wahaApiUrl = '';
    public $wahaApiKey = '';
    public $isConnected = false;

    public function mount()
    {
        $config = Config::where('user_id', Auth::id())->first();
        if ($config) {
            $this->wahaApiUrl = $config->api_url ?? '';
            $this->wahaApiKey = $config->api_key ?? '';
        }
        $this->checkConnection();
    }


    public function saveConfiguration()
    {
        // Validate inputs with additional security checks
        $this->validate([
            'wahaApiUrl' => 'required|url|regex:/^https?:\/\/.+/i|max:500',
            'wahaApiKey' => 'required|string|min:10|max:1000|regex:/^[a-zA-Z0-9_\-]+$/',
        ], [
            'wahaApiUrl.required' => 'The base URL is required.',
            'wahaApiUrl.url' => 'The base URL must be a valid URL.',
            'wahaApiUrl.regex' => 'The URL must start with http:// or https://.',
            'wahaApiUrl.max' => 'The URL is too long.',
            'wahaApiKey.required' => 'The API key is required.',
            'wahaApiKey.min' => 'The API key must be at least 10 characters long.',
            'wahaApiKey.max' => 'The API key is too long.',
            'wahaApiKey.regex' => 'The API key contains invalid characters. Only letters, numbers, hyphens, and underscores are allowed.',
        ]);

        try {
            // Update or create config in database
            $config = Config::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'api_url' => $this->wahaApiUrl,
                    'api_key' => $this->wahaApiKey,
                ]
            );

            // Check connection after saving configuration
            $this->checkConnection();

            Log::info('WAHA configuration saved successfully.', [
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'WAHA configuration saved successfully.');

            return $this->redirect('/waha', true);
        } catch (\Exception $e) {
            // Log the specific error
            Log::error('Failed to save WAHA configuration', [
                'error' => $e->getMessage(),
                'user_id' => Auth::user()->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            session()->flash('error', 'Failed to save WAHA configuration. Please try again or contact an administrator.');

            return $this->redirect('/waha', true);
        }
    }

    public function checkConnection()
    {
        try {
            $config = Config::where('user_id', Auth::id())->first();

            if ($config && $config->api_url && $config->api_key) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => $config->api_key,
                ])->get($config->api_url . '/health');

                $this->isConnected = $response->successful() && $response->status() === 200;
            } else {
                $this->isConnected = false;
            }
        } catch (\Exception $e) {
            $this->isConnected = false;
            Log::warning('Failed to check WAHA connection: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.waha.waha-index');
    }
}
