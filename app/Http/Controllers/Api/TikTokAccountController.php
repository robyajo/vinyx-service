<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveEventResource;
use App\Http\Resources\LiveSessionResource;
use App\Http\Resources\TikTokAccountResource;
use App\Http\Traits\ApiResponse;
use App\Models\TikTokAccount;
use App\Models\LiveSession;
use App\Models\LiveEvent;
use App\Services\ListenerStreamService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        return $this->successResponse(TikTokAccountResource::collection($accounts), 'Daftar akun TikTok');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'uniqueId' => 'required|string|max:50|unique:tiktok_accounts,unique_id',
            'unique_id' => 'sometimes|string|max:50|unique:tiktok_accounts,unique_id',
            'avatar_url' => 'nullable|url',
            'avatarUrl' => 'nullable|url',
            'proxy' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $data = $validator->validated();
        $data['unique_id'] = $data['unique_id'] ?? $data['uniqueId'];
        $data['avatar_url'] = $data['avatar_url'] ?? $data['avatarUrl'] ?? null;
        unset($data['uniqueId'], $data['avatarUrl']);

        if (!$data['avatar_url']) {
            $fetched = $this->fetchAvatarFromTikTok($data['unique_id']);
            if ($fetched) {
                $data['avatar_url'] = $fetched;
            }
        }

        $account = TikTokAccount::create([
            'user_id' => $request->user()->id,
            ...$data,
        ]);

        return $this->createdResponse(new TikTokAccountResource($account), 'Akun TikTok dibuat');
    }

    private function fetchAvatarFromTikTok(string $uniqueId): ?string
    {
        try {
            $response = Http::timeout(5)
                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
                ->get("https://www.tiktok.com/@{$uniqueId}");

            if (!$response->successful()) return null;

            $html = $response->body();

            if (preg_match('/<meta\s+property="og:image"\s+content="([^"]+)"/i', $html, $m)) {
                return $m[1];
            }
            if (preg_match('/<meta\s+name="og:image"\s+content="([^"]+)"/i', $html, $m)) {
                return $m[1];
            }

            return null;
        } catch (ConnectionException) {
            return null;
        }
    }

    public function show(Request $request, TikTokAccount $account): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $account->load(['liveSessions' => fn($q) => $q->latest()]);

        return $this->successResponse(new TikTokAccountResource($account), 'Detail akun TikTok');
    }

    public function update(Request $request, TikTokAccount $account): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:50',
            'uniqueId' => 'sometimes|string|max:50|unique:tiktok_accounts,unique_id,' . $account->id,
            'unique_id' => 'sometimes|string|max:50|unique:tiktok_accounts,unique_id,' . $account->id,
            'avatar_url' => 'nullable|url',
            'avatarUrl' => 'nullable|url',
            'proxy' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $data = $validator->validated();
        if (isset($data['uniqueId']) && !isset($data['unique_id'])) {
            $data['unique_id'] = $data['uniqueId'];
        }
        if (isset($data['avatarUrl']) && !isset($data['avatar_url'])) {
            $data['avatar_url'] = $data['avatarUrl'];
        }
        unset($data['uniqueId'], $data['avatarUrl']);

        $account->update($data);

        return $this->successResponse(new TikTokAccountResource($account), 'Akun TikTok diperbarui');
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

    public function connect(Request $request, TikTokAccount $account, ListenerStreamService $listener): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $activeSession = LiveSession::where('account_id', $account->id)
            ->where('status', 'CONNECTED')
            ->first();

        if ($activeSession) {
            return $this->errorResponse('Akun sudah terhubung ke live', 400);
        }

        $result = $listener->connect($account->id, (string) $request->user()->id);

        if (!$result['success']) {
            return $this->errorResponse($result['error'], 502);
        }

        $sessionData = $result['session'];
        $avatarUrl = $sessionData['avatarUrl'] ?? null;

        if ($avatarUrl && $avatarUrl !== $account->avatar_url) {
            $account->update(['avatar_url' => $avatarUrl]);
        }

        $session = LiveSession::create([
            'account_id' => $account->id,
            'status' => 'CONNECTED',
            'started_at' => now(),
            'metadata' => $sessionData['metadata'] ?? [],
        ]);

        $account->update(['is_connected' => true]);

        return $this->successResponse(new LiveSessionResource($session), 'Terhubung ke live TikTok');
    }

    public function disconnect(Request $request, TikTokAccount $account, ListenerStreamService $listener): JsonResponse
    {
        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Akun TikTok tidak ditemukan');
        }

        $result = $listener->disconnect($account->id, (string) $request->user()->id);

        $activeSession = LiveSession::where('account_id', $account->id)
            ->where('status', 'CONNECTED')
            ->first();

        if ($activeSession) {
            $activeSession->update([
                'status' => 'DISCONNECTED',
                'ended_at' => now(),
            ]);
        }

        $account->update(['is_connected' => false]);

        return $this->successResponse(
            ['session' => $activeSession ? new LiveSessionResource($activeSession->fresh()) : null],
            $result ? 'Terputus dari live TikTok' : 'Gagal memutus koneksi, tetapi status dibersihkan'
        );
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

        return $this->successResponse(LiveSessionResource::collection($sessions), 'Riwayat sesi live');
    }

    public function sessionDetail(Request $request, LiveSession $session): JsonResponse
    {
        $account = $session->account;

        if ($account->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Sesi tidak ditemukan');
        }

        $session->load(['account', 'liveEvents' => fn($q) => $q->latest()->limit(50)]);
        $session->loadCount('liveEvents');

        return $this->successResponse(new LiveSessionResource($session), 'Detail sesi live');
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
            'events' => LiveEventResource::collection($events),
            'cursor' => $nextCursor,
        ], 'Event sesi live');
    }
}
