<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\StockSignal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\StockSignalResource;

final class SignalController extends ApiController
{
    /**
     * Get published stock signals (subscription required).
     * Query: q (kode emiten), signal_type, per_page.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->loadMissing('subscription');

        if (! $user->hasActiveSubscription()) {
            return $this->forbidden('Subscription premium diperlukan untuk mengakses Sinyal Saham.');
        }

        $query = StockSignal::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['stockCompany'])
            ->orderByDesc('published_at');

        $search = $request->filled('q') ? trim((string) $request->q) : '';
        if ($search !== '') {
            $query->where('kode_emiten', 'like', '%' . $search . '%');
        }

        if ($request->filled('signal_type')) {
            $query->where('signal_type', (string) $request->signal_type);
        }

        $perPage = min(max((int) $request->get('per_page', 15), 1), 50);
        $signals = $query->paginate($perPage);

        return $this->success([
            'signals' => StockSignalResource::collection($signals->getCollection())->resolve(),
            'meta' => [
                'current_page' => $signals->currentPage(),
                'last_page' => $signals->lastPage(),
                'per_page' => $signals->perPage(),
                'total' => $signals->total(),
            ],
        ]);
    }
}

