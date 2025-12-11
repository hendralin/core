<?php

namespace App\Livewire\Waha;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
            // Update .env file
            $envPath = base_path('.env');

            // Check if .env file exists and is writable
            if (!file_exists($envPath)) {
                throw new \Exception('.env file does not exist');
            }

            if (!is_writable($envPath)) {
                throw new \Exception('.env file is not writable. Please check file permissions.');
            }

            $envContent = file_get_contents($envPath);

            // Create backup of original .env file
            $envBackup = $envPath . '.backup.' . time();
            if (!file_put_contents($envBackup, $envContent)) {
                throw new \Exception('Failed to create backup of .env file');
            }

            // Update or add WAHA_API_URL (safely escaped)
            $urlReplacement = 'WAHA_API_URL=' . $this->escapeEnvValue($this->wahaApiUrl);
            if (preg_match('/^WAHA_API_URL=.*/m', $envContent)) {
                $envContent = preg_replace('/^WAHA_API_URL=.*/m', $urlReplacement, $envContent);
            } else {
                $envContent .= "\n" . $urlReplacement;
            }

            // Update or add WAHA_API_KEY (safely escaped)
            $keyReplacement = 'WAHA_API_KEY=' . $this->escapeEnvValue($this->wahaApiKey);
            if (preg_match('/^WAHA_API_KEY=.*/m', $envContent)) {
                $envContent = preg_replace('/^WAHA_API_KEY=.*/m', $keyReplacement, $envContent);
            } else {
                $envContent .= "\n" . $keyReplacement;
            }

            // Write to .env file atomically
            if (!file_put_contents($envPath, $envContent)) {
                // Restore from backup if write failed
                file_put_contents($envPath, file_get_contents($envBackup));
                unlink($envBackup);
                throw new \Exception('Failed to write to .env file');
            }

            // Clean up backup file
            unlink($envBackup);

            // Clear config cache
            Artisan::call('config:cache');

            // Check connection after saving configuration
            $this->checkConnection();

            Log::info('WAHA configuration saved successfully.');

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

            // Provide user-friendly error message based on error type
            $errorMessage = 'Failed to save WAHA configuration.';
            if (str_contains($e->getMessage(), 'backup')) {
                $errorMessage .= ' Unable to create backup file.';
            } elseif (str_contains($e->getMessage(), 'write')) {
                $errorMessage .= ' Unable to write to configuration file.';
            } elseif (str_contains($e->getMessage(), 'cache')) {
                $errorMessage .= ' Configuration saved but cache refresh failed.';
            } else {
                $errorMessage .= ' Please try again or contact an administrator.';
            }

            session()->flash('error', $errorMessage);
            return $this->redirect('/waha', true);
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

    /**
     * Properly escape and quote environment variable values
     */
    private function escapeEnvValue(string $value): string
    {
        // If the value contains spaces, quotes, or special characters, wrap it in quotes
        if (preg_match('/[\s#"\'\\\\]/', $value)) {
            // Escape any existing quotes and wrap in double quotes
            $escaped = addslashes($value);
            return '"' . $escaped . '"';
        }

        return $value;
    }

    public function render()
    {
        return view('livewire.waha.waha-index');
    }
}
