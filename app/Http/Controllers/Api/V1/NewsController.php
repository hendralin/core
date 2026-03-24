<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Services\News\NewsAggregatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NewsController extends ApiController
{
    public function __construct(
        private readonly NewsAggregatorService $newsAggregator,
    ) {}

    /**
     * Headline berita terkait emiten (internal idx_news + opsional Google News RSS).
     *
     * Query: code (required), limit (1-20, default 10), days (1-365, default 30), include_live (bool, default true)
     */
    public function headlines(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'min:2', 'max:10'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'days' => ['sometimes', 'integer', 'min:1', 'max:365'],
            'include_live' => ['sometimes', 'boolean'],
        ]);

        $code = (string) $validated['code'];
        $limit = isset($validated['limit']) ? (int) $validated['limit'] : 10;
        $days = isset($validated['days']) ? (int) $validated['days'] : 30;
        $includeLive = $request->boolean('include_live', true);

        $payload = $this->newsAggregator->headlines(
            $code,
            $limit,
            $days,
            $includeLive,
            false,
        );

        return $this->success($payload);
    }
}
