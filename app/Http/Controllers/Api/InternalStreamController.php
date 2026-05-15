<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\LiveSession;
use App\Models\LiveEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InternalStreamController extends Controller
{
    use ApiResponse;

    protected function checkApiKey(Request $request): void
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');

        if (!$apiKey || $apiKey !== config('app.listener_api_key')) {
            abort(401, 'Invalid API key');
        }
    }

    public function storeSession(Request $request): JsonResponse
    {
        $this->checkApiKey($request);

        $validator = Validator::make($request->all(), [
            'id' => 'nullable|string',
            'account_id' => 'required|integer',
            'status' => 'required|in:CONNECTED,DISCONNECTED,RECONNECTING,ERROR',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $session = LiveSession::create($validator->validated());

        return $this->createdResponse($session, 'Session tersimpan');
    }

    public function updateSession(Request $request, string $session): JsonResponse
    {
        $this->checkApiKey($request);

        $session = LiveSession::find($session);
        if (!$session) {
            return $this->notFoundResponse('Session tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:CONNECTED,DISCONNECTED,RECONNECTING,ERROR',
            'ended_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $session->update($validator->validated());

        return $this->successResponse($session, 'Session diperbarui');
    }

    public function storeEvent(Request $request): JsonResponse
    {
        $this->checkApiKey($request);

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:live_sessions,id',
            'type' => 'required|string|max:50',
            'data' => 'nullable|array',
            'raw' => 'nullable|array',
            'timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $event = LiveEvent::create($validator->validated());

        return $this->createdResponse($event, 'Event tersimpan');
    }

    public function storeEvents(Request $request): JsonResponse
    {
        $this->checkApiKey($request);

        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*.session_id' => 'required|exists:live_sessions,id',
            'events.*.type' => 'required|string|max:50',
            'events.*.data' => 'nullable|array',
            'events.*.raw' => 'nullable|array',
            'events.*.timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $events = [];
        foreach ($request->input('events') as $eventData) {
            $events[] = LiveEvent::create($eventData);
        }

        return $this->createdResponse($events, count($events) . ' event tersimpan');
    }

    public function updateUserGems(Request $request): JsonResponse
    {
        $this->checkApiKey($request);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'gems' => 'required|integer|min:0',
            'events_used' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = User::find($request->input('user_id'));
        $data = ['gems' => $request->input('gems')];

        if ($request->has('events_used')) {
            $data['events_used'] = $request->input('events_used');
        }

        $user->update($data);

        return $this->successResponse([
            'gems' => (int) $user->gems,
            'events_used' => (int) ($user->events_used ?? 0),
        ], 'User gems updated');
    }
}
