<?php

namespace App\Livewire\Waha;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class WahaIndex extends Component
{
    public $wahaApiUrl = '';
    public $wahaApiKey = '';
    public $isConnected = false;

    public function mount()
    {
        $this->wahaApiUrl = env('WAHA_API_URL', '');
        $this->wahaApiKey = env('WAHA_API_KEY', '');
        $this->checkConnection();
    }


    public function saveConfiguration()
    {
        // Validate inputs
        $this->validate([
            'wahaApiUrl' => 'required|url',
            'wahaApiKey' => 'required|string|min:10',
        ], [
            'wahaApiUrl.required' => 'The base URL is required.',
            'wahaApiUrl.url' => 'The base URL must be a valid URL.',
            'wahaApiKey.required' => 'The API key is required.',
            'wahaApiKey.min' => 'The API key must be at least 10 characters long.',
        ]);

        try {
            // Update .env file
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Update or add WAHA_API_URL
            if (preg_match('/^WAHA_API_URL=.*/m', $envContent)) {
                $envContent = preg_replace('/^WAHA_API_URL=.*/m', "WAHA_API_URL={$this->wahaApiUrl}", $envContent);
            } else {
                $envContent .= "\nWAHA_API_URL={$this->wahaApiUrl}";
            }

            // Update or add WAHA_API_KEY
            if (preg_match('/^WAHA_API_KEY=.*/m', $envContent)) {
                $envContent = preg_replace('/^WAHA_API_KEY=.*/m', "WAHA_API_KEY={$this->wahaApiKey}", $envContent);
            } else {
                $envContent .= "\nWAHA_API_KEY={$this->wahaApiKey}";
            }

            file_put_contents($envPath, $envContent);

            // Clear config cache
            Artisan::call('config:cache');

            // Check connection after saving configuration
            $this->checkConnection();

            Log::info('WAHA configuration saved successfully.');

            return $this->redirect('/waha', true);
        } catch (\Exception $e) {
            Log::error('Failed to save WAHA configuration: ' . $e->getMessage());
        }
    }

    public function checkConnection()
    {
        try {
            if (env('WAHA_API_URL') && env('WAHA_API_KEY')) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => env('WAHA_API_KEY'),
                ])->get(env('WAHA_API_URL') . '/health');

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
