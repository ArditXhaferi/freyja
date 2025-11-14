<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VoiceSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceSessionController extends Controller
{
    /**
     * Create a new voice session
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'started_at' => 'required|date',
            'status' => 'sometimes|string|in:pending,complete,failed',
            'model' => 'sometimes|string|max:255',
        ]);

        $voiceSession = VoiceSession::create([
            'user_id' => $user->id,
            'started_at' => \Carbon\Carbon::parse($validated['started_at']),
            'status' => $validated['status'] ?? 'pending',
            'model' => $validated['model'] ?? 'elevenlabs-scribe-v2',
            'transcript' => '',
            'ai_message' => '',
        ]);

        return response()->json([
            'message' => 'Voice session created',
            'id' => $voiceSession->id,
            'voice_session' => $voiceSession,
        ], 201);
    }

    /**
     * Update a voice session
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $voiceSession = VoiceSession::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'transcript' => 'sometimes|string',
            'ai_message' => 'sometimes|string',
            'status' => 'sometimes|string|in:pending,complete,failed',
            'completed_at' => 'sometimes|date',
            'audio_path' => 'sometimes|string|max:255',
        ]);

        if (isset($validated['completed_at'])) {
            $validated['completed_at'] = \Carbon\Carbon::parse($validated['completed_at']);
        }

        $voiceSession->fill($validated);
        $voiceSession->save();

        return response()->json([
            'message' => 'Voice session updated',
            'voice_session' => $voiceSession,
        ], 200);
    }
}

