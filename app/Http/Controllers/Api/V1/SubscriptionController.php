<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\SubscriptionResource;

final class SubscriptionController extends ApiController
{
    /**
     * Get the authenticated user's subscription status.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->loadMissing('subscription');

        return $this->success([
            'has_active_subscription' => $user->hasActiveSubscription(),
            'subscription' => $user->subscription ? SubscriptionResource::make($user->subscription)->resolve() : null,
        ]);
    }
}

