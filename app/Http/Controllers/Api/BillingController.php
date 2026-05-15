<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    use ApiResponse;

    public function gems(Request $request): JsonResponse
    {
        $user = $request->user();

        $gems = (int) ($user->gems ?? 0);
        $eventsUsed = (int) ($user->events_used ?? 0);
        $plan = 'Free';
        $maxInteractions = 100;
        $isUnlimited = false;
        $subscription = null;

        $eventsRemaining = max(0, $maxInteractions - $eventsUsed);

        return $this->successResponse([
            'gems' => $gems,
            'plan' => $plan,
            'maxInteractions' => $maxInteractions,
            'eventsUsed' => $eventsUsed,
            'eventsRemaining' => $eventsRemaining,
            'isUnlimited' => $isUnlimited,
            'subscription' => $subscription,
        ], 'Gem and subscription info');
    }
}
