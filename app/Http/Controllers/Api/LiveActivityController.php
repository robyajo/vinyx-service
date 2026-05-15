<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LiveActivityController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string',
            'username' => 'required|string',
            'type' => 'required|string',
            'nickname' => 'required|string',
            'content' => 'nullable|string',
            'count' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Log aktivitas ke file atau database sesuai kebutuhan
        // Untuk saat ini simpan di log file
        logger()->channel('stack')->info('Live Activity', $validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas tercatat',
        ]);
    }
}
