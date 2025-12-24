<?php

namespace App\Livewire\Dashboard;

use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use App\Models\Schedule;
use App\Models\Session;
use App\Models\Template;
use App\Models\Config;
use App\Traits\HasWahaConfig;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

#[Title('Dashboard')]
class DashboardIndex extends Component
{
    use HasWahaConfig;

    public $startDate;
    public $endDate;

    public function mount()
    {
        $userTimezone = $this->getUserTimezone();
        $now = Carbon::now($userTimezone);

        // Set default to start and end of current month
        $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
        $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        // Validate that start date is not after end date
        if ($this->startDate && $this->endDate && $this->startDate > $this->endDate) {
            $this->endDate = $this->startDate;
        }
    }

    public function updatedEndDate()
    {
        // Validate that end date is not before start date
        if ($this->startDate && $this->endDate && $this->endDate < $this->startDate) {
            $this->startDate = $this->endDate;
        }
    }

    /**
     * Get user's timezone or default to app timezone
     */
    private function getUserTimezone()
    {
        $user = Auth::user();
        return $user->timezone ?? config('app.timezone', 'UTC');
    }

    /**
     * Get current time in user's timezone
     */
    private function nowInUserTimezone()
    {
        return Carbon::now($this->getUserTimezone());
    }

    public function render()
    {
        $userId = Auth::id();

        // Get all statistics
        $statistics = $this->getStatistics($userId);

        // Get WAHA server information
        $wahaInfo = $this->getWahaInfo();

        // Get queue information
        $queueInfo = $this->getQueueInfo();

        // Get recent messages
        $recentMessages = $this->getRecentMessages($userId);

        // Get message trends (last 7 days)
        $messageTrends = $this->getMessageTrends($userId);

        // Get active sessions
        $activeSessions = $this->getActiveSessions($userId);

        // Get upcoming schedules
        $upcomingSchedules = $this->getUpcomingSchedules($userId);

        // Get current time in user's timezone
        $currentTime = $this->nowInUserTimezone();
        $userTimezone = $this->getUserTimezone();

        return view('livewire.dashboard.dashboard-index', compact(
            'statistics',
            'wahaInfo',
            'queueInfo',
            'recentMessages',
            'messageTrends',
            'activeSessions',
            'upcomingSchedules',
            'currentTime',
            'userTimezone'
        ));
    }

    private function getStatistics($userId)
    {
        $userTimezone = $this->getUserTimezone();
        $now = $this->nowInUserTimezone();

        // Get date range filter
        $dateRange = $this->getDateRangeFilter();

        return [
            // Messages
            'total_messages' => Message::where('created_by', $userId)
                ->when($dateRange, function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->count(),
            'messages_today' => Message::where('created_by', $userId)
                ->whereBetween('created_at', [
                    $now->copy()->startOfDay()->utc(),
                    $now->copy()->endOfDay()->utc()
                ])
                ->count(),
            'messages_this_week' => Message::where('created_by', $userId)
                ->where('created_at', '>=', $now->copy()->startOfWeek()->utc())
                ->count(),
            'messages_this_month' => Message::where('created_by', $userId)
                ->where('created_at', '>=', $now->copy()->startOfMonth()->utc())
                ->count(),
            'sent_messages' => Message::where('created_by', $userId)
                ->where('status', 'sent')
                ->when($dateRange, function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->count(),
            'failed_messages' => Message::where('created_by', $userId)
                ->where('status', 'failed')
                ->when($dateRange, function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->count(),
            'pending_messages' => Message::where('created_by', $userId)
                ->where(function($q) {
                    $q->where('status', 'pending')->orWhereNull('status');
                })
                ->when($dateRange, function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->count(),

            // Contacts & Groups
            'total_contacts' => Contact::forUser($userId)->count(),
            'total_groups' => Group::forUser($userId)->count(),
            'communities' => Group::forUser($userId)->where('detail->isCommunity', true)->count(),
            'regular_groups' => Group::forUser($userId)->where('detail->isCommunity', false)->count(),

            // Sessions
            'total_sessions' => Session::where('created_by', $userId)->count(),
            'active_sessions' => Session::where('created_by', $userId)->where('is_active', true)->count(),

            // Templates
            'total_templates' => Template::where('created_by', $userId)->count(),
            'active_templates' => Template::where('created_by', $userId)->where('is_active', true)->count(),

            // Schedules
            'total_schedules' => Schedule::where('created_by', $userId)->count(),
            'active_schedules' => Schedule::where('created_by', $userId)->where('is_active', true)->count(),
            'total_schedule_runs' => Schedule::where('created_by', $userId)->sum('usage_count'),
        ];
    }

    private function getWahaInfo()
    {
        $wahaInfo = [
            'configured' => false,
            'connected' => false,
            'api_url' => null,
            'version' => null,
            'status' => 'Not Configured',
            'status_color' => 'gray',
        ];

        try {
            $config = Config::where('user_id', Auth::id())->first();
            $apiUrl = $config?->api_url;
            $apiKey = $config?->api_key;

            if ($apiUrl && $apiKey) {
                $wahaInfo['configured'] = true;
                $wahaInfo['api_url'] = $apiUrl;

                // Check connection and get version info
                try {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(5)->withHeaders([
                        'accept' => 'application/json',
                        'X-Api-Key' => $apiKey,
                    ])->get($apiUrl . '/health');

                    if ($response->successful()) {
                        $wahaInfo['connected'] = true;
                        $wahaInfo['status'] = 'Connected';
                        $wahaInfo['status_color'] = 'green';

                        // Try to get version info
                        try {
                            /** @var \Illuminate\Http\Client\Response $versionResponse */
                            $versionResponse = Http::timeout(5)->withHeaders([
                                'accept' => 'application/json',
                                'X-Api-Key' => $apiKey,
                            ])->get($apiUrl . '/api/version');

                            if ($versionResponse->successful()) {
                                $versionData = $versionResponse->json();
                                $wahaInfo['version'] = $versionData['version'] ?? 'Unknown';
                            }
                        } catch (\Exception $e) {
                            $wahaInfo['version'] = 'Unknown';
                        }
                    } else {
                        $wahaInfo['status'] = 'Disconnected';
                        $wahaInfo['status_color'] = 'red';
                    }
                } catch (\Exception $e) {
                    $wahaInfo['status'] = 'Disconnected';
                    $wahaInfo['status_color'] = 'red';
                }
            } else {
                $wahaInfo['status'] = 'Not Configured';
                $wahaInfo['status_color'] = 'yellow';
            }
        } catch (\Exception $e) {
            Log::warning('Failed to check WAHA status: ' . $e->getMessage());
            $wahaInfo['status'] = 'Error';
            $wahaInfo['status_color'] = 'red';
        }

        return $wahaInfo;
    }

    private function getQueueInfo()
    {
        $userId = Auth::id();
        $queueInfo = [
            'connection' => config('queue.default'),
            'configured' => true,
            'pending_jobs' => 0,
            'failed_jobs' => 0,
            'status' => 'Unknown',
            'status_color' => 'gray',
        ];

        try {
            if ($queueInfo['connection'] === 'sync') {
                $queueInfo['status'] = 'Synchronous (Not Recommended)';
                $queueInfo['configured'] = false;
                $queueInfo['status_color'] = 'yellow';
            } else {
                $queueInfo['status'] = 'Configured';
                $queueInfo['status_color'] = 'green';

                if ($queueInfo['connection'] === 'database') {
                    try {
                        // Count pending jobs by counting pending messages for this user
                        // Each job is created for one message, so pending messages = pending jobs
                        $queueInfo['pending_jobs'] = Message::where('created_by', $userId)
                            ->where(function($q) {
                                $q->where('status', 'pending')->orWhereNull('status');
                            })
                            ->count();

                        // Count failed jobs by counting failed messages for this user
                        $queueInfo['failed_jobs'] = Message::where('created_by', $userId)
                            ->where('status', 'failed')
                            ->count();
                    } catch (\Exception $e) {
                        Log::warning('Could not get queue statistics: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get queue info: ' . $e->getMessage());
            $queueInfo['status'] = 'Error';
            $queueInfo['status_color'] = 'red';
        }

        return $queueInfo;
    }

    private function getDateRangeFilter()
    {
        if (!$this->startDate || !$this->endDate) {
            return null;
        }

        $userTimezone = $this->getUserTimezone();
        $start = Carbon::parse($this->startDate, $userTimezone)->startOfDay()->utc();
        $end = Carbon::parse($this->endDate, $userTimezone)->endOfDay()->utc();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private function getRecentMessages($userId, $limit = 5)
    {
        $dateRange = $this->getDateRangeFilter();

        return Message::where('created_by', $userId)
            ->when($dateRange, function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->with(['wahaSession', 'createdBy'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getMessageTrends($userId)
    {
        $trends = [];
        $userTimezone = $this->getUserTimezone();
        $dateRange = $this->getDateRangeFilter();

        // If date range is set, use it; otherwise use last 7 days
        if ($dateRange) {
            $startDate = Carbon::parse($this->startDate, $userTimezone)->startOfDay();
            $endDate = Carbon::parse($this->endDate, $userTimezone)->endOfDay();
            $daysDiff = $startDate->diffInDays($endDate);

            // Limit to 30 days for performance
            $daysToShow = min($daysDiff + 1, 30);

            // First, collect all data in the period
            $allTrends = [];
            $firstDataDate = null;
            $lastDataDate = null;

            for ($i = 0; $i < $daysToShow; $i++) {
                $date = $startDate->copy()->addDays($i);
                $dateStart = $date->copy()->startOfDay()->utc();
                $dateEnd = $date->copy()->endOfDay()->utc();

                $count = Message::where('created_by', $userId)
                    ->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->count();

                if ($count > 0) {
                    if ($firstDataDate === null) {
                        $firstDataDate = $i;
                    }
                    $lastDataDate = $i;
                }

                $allTrends[] = [
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->format('D, M d'),
                    'count' => $count,
                ];
            }

            // Only return trends from first data date to last data date (or end of period)
            if ($firstDataDate !== null) {
                $endIndex = $lastDataDate !== null ? $lastDataDate : ($daysToShow - 1);
                $trends = array_slice($allTrends, $firstDataDate, $endIndex - $firstDataDate + 1);
            }
        } else {
            // Default: last 7 days
            $now = $this->nowInUserTimezone();
            $startDate = $now->copy()->subDays(6)->startOfDay();

            // Collect all data first
            $allTrends = [];
            $firstDataDate = null;
            $lastDataDate = null;

            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i);
                $dateStart = $date->copy()->startOfDay()->utc();
                $dateEnd = $date->copy()->endOfDay()->utc();

                $count = Message::where('created_by', $userId)
                    ->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->count();

                if ($count > 0) {
                    if ($firstDataDate === null) {
                        $firstDataDate = $i;
                    }
                    $lastDataDate = $i;
                }

                $allTrends[] = [
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->format('D, M d'),
                    'count' => $count,
                ];
            }

            // Only return trends from first data date to last data date (or end of period)
            if ($firstDataDate !== null) {
                $endIndex = $lastDataDate !== null ? $lastDataDate : 6;
                $trends = array_slice($allTrends, $firstDataDate, $endIndex - $firstDataDate + 1);
            }
        }

        return $trends;
    }

    private function getActiveSessions($userId)
    {
        $dbSessions = Session::where('created_by', $userId)
            ->where('is_active', true)
            ->withCount('messages')
            ->latest()
            ->limit(6) // Limit to 6 for dashboard
            ->get();

        // If WAHA is not configured, return database sessions only
        if (!$this->isWahaConfigured()) {
            return $dbSessions->map(function ($session) {
                $session->profile_picture = null;
                $session->me = null;
                return $session;
            });
        }

        $apiUrl = $this->getWahaApiUrl();
        $apiKey = $this->getWahaApiKey();

        if (!$apiUrl || !$apiKey) {
            return $dbSessions->map(function ($session) {
                $session->profile_picture = null;
                $session->me = null;
                return $session;
            });
        }

        try {
            // Cache sessions data for 5 minutes to reduce API calls
            $cacheKey = 'waha_sessions_data_' . $userId;
            $apiSessions = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($apiUrl, $apiKey) {
                try {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::withHeaders([
                        'accept' => 'application/json',
                        'X-Api-Key' => $apiKey,
                    ])->get($apiUrl . "/api/sessions?all=true");

                    if (method_exists($response, 'successful') && $response->successful()) {
                        return method_exists($response, 'json') ? $response->json() : null;
                    }
                    return null;
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch sessions from WAHA API: ' . $e->getMessage());
                    return null;
                }
            });

            if ($apiSessions) {
                $dbSessionsBySessionId = $dbSessions->keyBy('session_id');
                $enrichedSessions = collect();

                foreach ($apiSessions as $apiSession) {
                    $sessionId = $apiSession['name'] ?? null;
                    if ($sessionId && isset($dbSessionsBySessionId[$sessionId])) {
                        $dbSession = $dbSessionsBySessionId[$sessionId];

                        // Get profile picture with caching (15 minutes - profile pictures don't change often)
                        $profilePicture = null;
                        if (isset($apiSession['me']['id'])) {
                            $profileCacheKey = 'profile_picture_' . $apiSession['me']['id'];
                            $profilePicture = Cache::remember($profileCacheKey, now()->addMinutes(15), function () use ($apiSession, $apiUrl, $apiKey, $sessionId) {
                                try {
                                    $contactId = urlencode($apiSession['me']['id']);
                                    /** @var \Illuminate\Http\Client\Response $profileResponse */
                                    $profileResponse = Http::withHeaders([
                                        'accept' => '*/*',
                                        'X-Api-Key' => $apiKey,
                                    ])->get($apiUrl . "/api/contacts/profile-picture?contactId={$contactId}&refresh=false&session={$sessionId}");

                                    if (method_exists($profileResponse, 'successful') && $profileResponse->successful()) {
                                        $profileData = method_exists($profileResponse, 'json') ? $profileResponse->json() : null;
                                        return $profileData['profilePictureURL'] ?? null;
                                    }
                                    return null;
                                } catch (\Exception $e) {
                                    Log::warning('Failed to get profile picture for session ' . $sessionId . ': ' . $e->getMessage());
                                    return null;
                                }
                            });
                        }

                        // Enrich session with API data
                        $dbSession->profile_picture = $profilePicture;
                        $dbSession->me = $apiSession['me'] ?? null;
                        $enrichedSessions->push($dbSession);
                    }
                }

                return $enrichedSessions;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch WAHA sessions for dashboard: ' . $e->getMessage());
        }

        // Fallback: return database sessions without API data
        return $dbSessions->map(function ($session) {
            $session->profile_picture = null;
            $session->me = null;
            return $session;
        });
    }

    private function getUpcomingSchedules($userId, $limit = 5)
    {
        $now = $this->nowInUserTimezone();

        return Schedule::where('created_by', $userId)
            ->where('is_active', true)
            ->where('next_run', '>=', $now->utc())
            ->with(['wahaSession'])
            ->orderBy('next_run', 'asc')
            ->limit($limit)
            ->get();
    }
}
