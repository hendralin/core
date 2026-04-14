<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Shetabit\Visitor\Models\Visit;

class AnalyticsService
{
    public function resolveRange(string $dateFrom, string $dateTo): array
    {
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();

        return [$from, $to];
    }

    /**
     * @return array{total:int, public_total:int, internal_total:int, unique_ips_public:int, unique_ips_all:int, online_public_estimate:int}
     */
    public function visitSummary(Carbon $from, Carbon $to): array
    {
        $base = Visit::query()->whereBetween('created_at', [$from, $to]);

        $total = (clone $base)->count();
        $publicTotal = (clone $base)->whereNull('visitor_id')->count();
        $internalTotal = (clone $base)->whereNotNull('visitor_id')->count();

        $uniqueIpsPublic = (int) Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNull('visitor_id')
            ->whereNotNull('ip')
            ->selectRaw('count(distinct ip) as c')
            ->value('c');

        $uniqueIpsAll = (int) Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('ip')
            ->selectRaw('count(distinct ip) as c')
            ->value('c');

        $onlineSince = now()->subMinutes(5);
        $onlinePublicEstimate = (int) Visit::query()
            ->where('created_at', '>=', $onlineSince)
            ->whereNull('visitor_id')
            ->whereNotNull('ip')
            ->selectRaw('count(distinct ip) as c')
            ->value('c');

        return [
            'total' => $total,
            'public_total' => $publicTotal,
            'internal_total' => $internalTotal,
            'unique_ips_public' => $uniqueIpsPublic,
            'unique_ips_all' => $uniqueIpsAll,
            'online_public_estimate' => $onlinePublicEstimate,
        ];
    }

    /**
     * @return array<int, array{day:string, count:int}>
     */
    public function dailyVisitTrend(Carbon $from, Carbon $to, bool $publicOnly = false): array
    {
        $dateExpr = DB::getDriverName() === 'sqlite'
            ? 'date(created_at)'
            : 'DATE(created_at)';

        $q = Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("{$dateExpr} as d")
            ->selectRaw('COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d');

        if ($publicOnly) {
            $q->whereNull('visitor_id');
        }

        $rows = $q->get();

        return $rows->map(fn ($row) => [
            'day' => (string) $row->d,
            'count' => (int) $row->c,
        ])->values()->all();
    }

    /**
     * @return array<int, array{label:string, count:int}>
     */
    public function browserBreakdown(Carbon $from, Carbon $to, bool $publicOnly = false): array
    {
        $q = Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('browser')
            ->where('browser', '!=', '')
            ->selectRaw('browser as label')
            ->selectRaw('COUNT(*) as c')
            ->groupBy('browser')
            ->orderByDesc('c')
            ->limit(12);

        if ($publicOnly) {
            $q->whereNull('visitor_id');
        }

        return $q->get()->map(fn ($row) => [
            'label' => (string) $row->label,
            'count' => (int) $row->c,
        ])->all();
    }

    /**
     * @return array<int, array{label:string, count:int}>
     */
    public function platformBreakdown(Carbon $from, Carbon $to, bool $publicOnly = false): array
    {
        $q = Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('platform')
            ->where('platform', '!=', '')
            ->selectRaw('platform as label')
            ->selectRaw('COUNT(*) as c')
            ->groupBy('platform')
            ->orderByDesc('c')
            ->limit(12);

        if ($publicOnly) {
            $q->whereNull('visitor_id');
        }

        return $q->get()->map(fn ($row) => [
            'label' => (string) $row->label,
            'count' => (int) $row->c,
        ])->all();
    }

    /**
     * @return array<int, array{label:string, count:int}>
     */
    public function deviceCategoryBreakdown(Carbon $from, Carbon $to, bool $publicOnly = false): array
    {
        $driver = DB::getDriverName();
        $case = match ($driver) {
            'sqlite' => "
                CASE
                    WHEN useragent IS NULL OR useragent = '' THEN 'Unknown'
                    WHEN LOWER(useragent) LIKE '%ipad%' OR LOWER(useragent) LIKE '%tablet%' THEN 'Tablet'
                    WHEN LOWER(useragent) LIKE '%mobile%' OR LOWER(useragent) LIKE '%iphone%' OR LOWER(useragent) LIKE '%android%' OR LOWER(useragent) LIKE '%ipod%' THEN 'Mobile'
                    ELSE 'Desktop'
                END
            ",
            default => "
                CASE
                    WHEN useragent IS NULL OR useragent = '' THEN 'Unknown'
                    WHEN LOWER(useragent) LIKE '%ipad%' OR LOWER(useragent) LIKE '%tablet%' THEN 'Tablet'
                    WHEN LOWER(useragent) LIKE '%mobile%' OR LOWER(useragent) LIKE '%iphone%' OR LOWER(useragent) LIKE '%android%' OR LOWER(useragent) LIKE '%ipod%' THEN 'Mobile'
                    ELSE 'Desktop'
                END
            ",
        };

        $q = Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("{$case} as category")
            ->selectRaw('COUNT(*) as c')
            ->groupBy('category')
            ->orderByDesc('c');

        if ($publicOnly) {
            $q->whereNull('visitor_id');
        }

        return $q->get()->map(fn ($row) => [
            'label' => (string) $row->category,
            'count' => (int) $row->c,
        ])->all();
    }

    /**
     * @return array<int, array{url:string, count:int}>
     */
    public function topUrls(Carbon $from, Carbon $to, bool $publicOnly = false, int $limit = 15): array
    {
        $q = Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('url')
            ->where('url', '!=', '')
            ->selectRaw('url')
            ->selectRaw('COUNT(*) as c')
            ->groupBy('url')
            ->orderByDesc('c')
            ->limit($limit);

        if ($publicOnly) {
            $q->whereNull('visitor_id');
        }

        return $q->get()->map(fn ($row) => [
            'url' => (string) $row->url,
            'count' => (int) $row->c,
        ])->all();
    }

    /**
     * @return array<int, array{id:int, label:string, visits:int}>
     */
    public function topVehiclesByVisits(Carbon $from, Carbon $to, int $limit = 10): array
    {
        $morph = Vehicle::class;

        $rows = Visit::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('visitable_type', $morph)
            ->whereNotNull('visitable_id')
            ->selectRaw('visitable_id as vehicle_id')
            ->selectRaw('COUNT(*) as c')
            ->groupBy('visitable_id')
            ->orderByDesc('c')
            ->limit($limit)
            ->get();

        $ids = $rows->pluck('vehicle_id')->filter()->all();
        $vehicles = Vehicle::query()
            ->with('brand')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($vehicles) {
            $v = $vehicles->get($row->vehicle_id);

            return [
                'id' => (int) $row->vehicle_id,
                'label' => $v ? ($v->police_number.' · '.($v->brand?->name ?? '—')) : '#'.$row->vehicle_id,
                'visits' => (int) $row->c,
            ];
        })->values()->all();
    }

    /**
     * @return array{page_views:int, chat_whatsapp:int, share_whatsapp:int, link_copy:int}
     */
    public function vehicleEngagementTotals(): array
    {
        return [
            'page_views' => (int) Vehicle::query()->sum('public_page_view_count'),
            'chat_whatsapp' => (int) Vehicle::query()->sum('chat_whatsapp_count'),
            'share_whatsapp' => (int) Vehicle::query()->sum('whatsapp_share_count'),
            'link_copy' => (int) Vehicle::query()->sum('link_copy_count'),
        ];
    }

    /**
     * @return array{login_events:int, total_activities:int, online_staff:int}
     */
    public function internalActivitySummary(Carbon $from, Carbon $to): array
    {
        $loginEvents = Activity::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('description', 'logged in to the system')
            ->count();

        $totalActivities = Activity::query()
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $onlineStaff = User::query()->online(180)->count();

        return [
            'login_events' => $loginEvents,
            'total_activities' => $totalActivities,
            'online_staff' => $onlineStaff,
        ];
    }

    /**
     * @return array<int, array{name:string, count:int}>
     */
    public function topActiveUsers(Carbon $from, Carbon $to, int $limit = 10): array
    {
        return Activity::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('causer_id')
            ->where('causer_type', User::class)
            ->selectRaw('causer_id')
            ->selectRaw('COUNT(*) as c')
            ->groupBy('causer_id')
            ->orderByDesc('c')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $user = User::query()->find($row->causer_id);

                return [
                    'name' => $user?->name ?? ('User #'.$row->causer_id),
                    'count' => (int) $row->c,
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array{day:string, count:int}>
     */
    public function dailyLoginTrend(Carbon $from, Carbon $to): array
    {
        $dateExpr = DB::getDriverName() === 'sqlite'
            ? 'date(created_at)'
            : 'DATE(created_at)';

        return Activity::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('description', 'logged in to the system')
            ->selectRaw("{$dateExpr} as d")
            ->selectRaw('COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->map(fn ($row) => [
                'day' => (string) $row->d,
                'count' => (int) $row->c,
            ])
            ->all();
    }
}
