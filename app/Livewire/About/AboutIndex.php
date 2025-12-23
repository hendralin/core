<?php

namespace App\Livewire\About;

use App\Models\Contact;
use App\Models\Group;
use App\Models\Template;
use App\Models\Message;
use App\Traits\HasWahaConfig;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Title('About The Broadcaster System v1.6.0')]
class AboutIndex extends Component
{
    use HasWahaConfig;
    public function render()
    {
        $systemInfo = [
            'version' => '1.6.0',
            'php_version' => PHP_VERSION,
            'laravel_version' => 'Laravel ' . app()->version(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'features' => [
                'contacts_management' => true,
                'groups_management' => true,
                'templates_system' => true,
                'broadcast_messaging' => true,
                'bulk_messaging' => true,
                'scheduled_messaging' => true,
                'schedules_management' => true,
                'multiple_message_types' => true,
                'timezone_aware_display' => true,
                'advanced_filtering' => true,
                'real_time_validation' => true,
                'waha_integration' => true,
                'profile_pictures' => true,
                'activity_logging' => true,
                'message_audit_trails' => true,
                'message_status_tracking' => true,
                'resend_functionality' => true,
                'queue_system' => true,
                'auto_retry' => true,
                'rate_limiting' => true,
                'laravel_scheduler' => true,
            ],
        ];

        // Check WAHA connection status
        $wahaInfo = $this->getWahaInfo();

        // Get system statistics
        $statistics = $this->getSystemStatistics();

        // Get queue information
        $queueInfo = $this->getQueueInfo();

        return view('livewire.about.about-index', compact('systemInfo', 'wahaInfo', 'statistics', 'queueInfo'));
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
            $config = \App\Models\Config::where('user_id', Auth::id())->first();
            $apiUrl = $config?->api_url;
            $apiKey = $config?->api_key;

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

                        if ($versionResponse && $versionResponse->successful()) {
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
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'messages_this_week' => Message::where('created_at', '>=', now()->startOfWeek())->count(),
            'sent_messages' => Message::where('status', 'sent')->count(),
            'failed_messages' => Message::where('status', 'failed')->count(),
            'pending_messages' => Message::where('status', 'pending')->orWhereNull('status')->count(),
            'schedules' => \App\Models\Schedule::count(),
            'active_schedules' => \App\Models\Schedule::where('is_active', true)->count(),
            'total_schedule_runs' => \App\Models\Schedule::sum('usage_count'),
        ];
    }

    private function getQueueInfo()
    {
        $queueInfo = [
            'connection' => config('queue.default'),
            'configured' => true,
            'pending_jobs' => 0,
            'failed_jobs' => 0,
            'status' => 'Unknown',
        ];

        try {
            // Check if queue connection is configured
            if ($queueInfo['connection'] === 'sync') {
                $queueInfo['status'] = 'Synchronous (Not Recommended)';
                $queueInfo['configured'] = false;
            } else {
                $queueInfo['status'] = 'Configured';

                // Try to get pending jobs count from database
                if ($queueInfo['connection'] === 'database') {
                    try {
                        $queueInfo['pending_jobs'] = DB::table('jobs')
                            ->where('queue', 'messages')
                            ->count();

                        $queueInfo['failed_jobs'] = DB::table('failed_jobs')->count();
                    } catch (\Exception $e) {
                        // Table might not exist or connection issue
                        Log::warning('Could not get queue statistics: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get queue info: ' . $e->getMessage());
            $queueInfo['status'] = 'Error';
        }

        return $queueInfo;
    }
}
