<?php

namespace App\Livewire\About;

use App\Models\Contact;
use App\Models\Group;
use App\Models\Template;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Title('About The Broadcaster System v1.3.0')]
class AboutIndex extends Component
{
    public function render()
    {
        $systemInfo = [
            'version' => '1.3.0',
            'php_version' => PHP_VERSION,
            'laravel_version' => 'Laravel ' . app()->version(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'features' => [
                'contacts_management' => true,
                'groups_management' => true,
                'templates_system' => true,
                'waha_integration' => true,
                'profile_pictures' => true,
                'activity_logging' => true,
            ],
        ];

        // Check WAHA connection status
        $wahaInfo = $this->getWahaInfo();

        // Get system statistics
        $statistics = $this->getSystemStatistics();

        return view('livewire.about.about-index', compact('systemInfo', 'wahaInfo', 'statistics'));
    }

    private function getWahaInfo()
    {
        $wahaInfo = [
            'configured' => false,
            'connected' => false,
            'api_url' => null,
            'version' => null,
            'status' => 'Not Configured'
        ];

        try {
            $apiUrl = env('WAHA_API_URL');
            $apiKey = env('WAHA_API_KEY');

            if ($apiUrl && $apiKey) {
                $wahaInfo['configured'] = true;
                $wahaInfo['api_url'] = $apiUrl;

                // Check connection and get version info
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => $apiKey,
                ])->get($apiUrl . '/health');

                if ($response->successful()) {
                    $wahaInfo['connected'] = true;
                    $wahaInfo['status'] = 'Connected';

                    // Try to get version info
                    try {
                        $versionResponse = Http::withHeaders([
                            'accept' => 'application/json',
                            'X-Api-Key' => $apiKey,
                        ])->get($apiUrl . '/api/version');

                        if ($versionResponse->successful()) {
                            $versionData = $versionResponse->json();
                            $wahaInfo['version'] = $versionData['version'] ?? 'Unknown';
                        }
                    } catch (\Exception $e) {
                        // Version info not available, that's okay
                        $wahaInfo['version'] = 'Unknown';
                    }
                } else {
                    $wahaInfo['status'] = 'Disconnected';
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to check WAHA status: ' . $e->getMessage());
            $wahaInfo['status'] = 'Error';
        }

        return $wahaInfo;
    }

    private function getSystemStatistics()
    {
        return [
            'contacts' => Contact::count(),
            'groups' => Group::count(),
            'templates' => Template::count(),
            'active_templates' => Template::where('is_active', true)->count(),
            'communities' => Group::where('detail->isCommunity', true)->count(),
            'regular_groups' => Group::where('detail->isCommunity', false)->count(),
        ];
    }
}
