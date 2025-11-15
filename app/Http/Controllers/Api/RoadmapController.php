<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roadmap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoadmapController extends Controller
{
    /**
     * Get the current user's roadmap
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $roadmap = Roadmap::where('user_id', $user->id)->first();

        if (!$roadmap) {
            return response()->json([
                'roadmap_json' => null,
                'message' => 'No roadmap found'
            ], 200);
        }

        return response()->json([
            'id' => $roadmap->id,
            'title' => $roadmap->title,
            'roadmap_json' => $roadmap->roadmap_json,
            'updated_by_ai_at' => $roadmap->updated_by_ai_at,
            'is_locked' => $roadmap->is_locked,
            'created_at' => $roadmap->created_at,
            'updated_at' => $roadmap->updated_at,
        ]);
    }

    /**
     * Update or create the user's roadmap
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'roadmap_json' => 'required|array',
            'roadmap_json.title' => 'sometimes|string|max:255',
            'roadmap_json.steps' => 'sometimes|array',
            'roadmap_json.steps.*.id' => 'sometimes|integer',
            'roadmap_json.steps.*.title' => 'sometimes|string',
            'roadmap_json.steps.*.description' => 'sometimes|string',
            'roadmap_json.steps.*.status' => 'sometimes|string|in:pending,in_progress,completed',
            'roadmap_json.steps.*.order' => 'sometimes|integer',
            'roadmap_json.steps.*.resources' => 'sometimes|array',
            'roadmap_json.steps.*.resources.*.title' => 'required_with:roadmap_json.steps.*.resources|string|max:255',
            'roadmap_json.steps.*.resources.*.url' => 'required_with:roadmap_json.steps.*.resources|url|max:500',
            'roadmap_json.steps.*.resources.*.description' => 'nullable|string|max:1000',
        ]);

        $roadmap = Roadmap::firstOrNew(['user_id' => $user->id]);

        // Update roadmap JSON
        $roadmap->roadmap_json = $validated['roadmap_json'];
        
        // Update title if provided in roadmap_json
        if (isset($validated['roadmap_json']['title'])) {
            $roadmap->title = $validated['roadmap_json']['title'];
        } elseif (!$roadmap->title) {
            $roadmap->title = 'My Startup Roadmap';
        }

        // Update AI timestamp
        $roadmap->updated_by_ai_at = now();

        $roadmap->save();

        return response()->json([
            'message' => 'Roadmap updated successfully',
            'roadmap' => [
                'id' => $roadmap->id,
                'title' => $roadmap->title,
                'roadmap_json' => $roadmap->roadmap_json,
                'updated_by_ai_at' => $roadmap->updated_by_ai_at,
            ]
        ], 200);
    }
}

