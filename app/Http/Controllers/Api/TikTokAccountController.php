<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\TikTokAccount;
use App\Models\LiveSession;
use App\Models\LiveEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TikTokAccountController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $accounts = TikTokAccount::where('user_id', $request->user()->id)
            ->with(['liveSessions' => fn($q) => $q->latest()->limit(1)])
            ->withCount('liveSessions')
            ->latest()
            ->get();

        return $this->successResponse($accounts, 'Daftar akun TikTok');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'unique_id' => 'required|string|max:50|unique:tiktok_accounts,unique_id',
            'avatar_url' => 'nullable|url',
            'proxy' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $account = TikTokAccount::create([
            'user_id' => $request->user()->id,
            ...$validator->validated(),
        ]);

        return $this->createdResponse($account, 'Akun TikTok dibuat');
    }

    public function show(Request $request, TikTokAccount $account): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $account->load(['liveSessions' => fn($q) => $q->latest()]);

        return $this->successResponse($account, 'Detail akun TikTok');
    }

    public function update(Request $request, TikTokAccount $account): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:50',
            'unique_id' => 'sometimes|string|max:50|unique:tiktok_accounts,unique_id,' . $account->id,
            'avatar_url' => 'nullable|url',
            'proxy' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $account->update($validator->validated());

        return $this->successResponse($account, 'Akun TikTok diperbarui');
    }

    public function destroy(Request $request, TikTokAccount $account): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $activeSession = LiveSession::where('account_id', $account->id)
            ->where('status', 'CONNECTED')
            ->first();

        if ($activeSession) {
            return $this->errorResponse('Putuskan sesi aktif sebelum menghapus akun', 400);
        }

        $account->delete();

        return $this->successResponse(null, 'Akun TikTok dihapus');
    }

    public function sessions(Request $request, TikTokAccount $account): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $sessions = LiveSession::where('account_id', $account->id)
            ->withCount('liveEvents')
            ->latest()
            ->get();

        return $this->successResponse($sessions, 'Riwayat sesi live');
    }

    public function sessionDetail(Request $request, LiveSession $session): JsonResponse
    {
        $account = $session->account;

        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Sesi tidak ditemukan');
        }

        $session->load(['account', 'liveEvents' => fn($q) => $q->latest()->limit(50)]);
        $session->loadCount('liveEvents');

        return $this->successResponse($session, 'Detail sesi live');
    }

    public function sessionEvents(Request $request, LiveSession $session): JsonResponse
    {
        $account = $session->account;

        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Sesi tidak ditemukan');
        }

        $limit = min((int) $request->input('limit', 50), 200);
        $cursor = $request->input('cursor');

        $query = LiveEvent::where('session_id', $session->id)
            ->orderBy('timestamp', 'desc');

        if ($cursor) {
            $cursorEvent = LiveEvent::find($cursor);
            if ($cursorEvent) {
                $query->where('timestamp', '<', $cursorEvent->timestamp)
                    ->orWhere(fn($q) => $q
                        ->where('timestamp', $cursorEvent->timestamp)
                        ->where('id', '<', $cursorEvent->id)
                    );
            }
        }

        $events = $query->take($limit + 1)->get();

        $nextCursor = null;
        if ($events->count() > $limit) {
            $last = $events->pop();
            $nextCursor = $last->id;
        }

        return $this->successResponse([
            'events' => $events,
            'next_cursor' => $nextCursor,
        ], 'Event sesi live');
    }
}
