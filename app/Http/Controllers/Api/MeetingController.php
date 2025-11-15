<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    /**
     * Check if user is ready to meet with an advisor
     */
    public function checkReadiness(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get business plan data
        $businessPlanFields = [
            'business_name', 'industry', 'business_idea', 'target_market_groups',
            'products_services_general', 'sales_marketing', 'address'
        ];

        $filledFields = 0;
        $missingFields = [];

        foreach ($businessPlanFields as $field) {
            $value = $user->$field;
            if ($value !== null && $value !== '') {
                $filledFields++;
            } else {
                $missingFields[] = $field;
            }
        }

        // Check roadmap progress
        $roadmap = $user->roadmap;
        $roadmapSteps = $roadmap ? $roadmap->roadmap_json : null;
        $hasRoadmapSteps = $roadmapSteps && isset($roadmapSteps['steps']) && count($roadmapSteps['steps']) > 0;

        // Determine readiness
        $isReady = $filledFields >= 5 && $hasRoadmapSteps;
        $readinessScore = ($filledFields / count($businessPlanFields)) * 100;

        return response()->json([
            'is_ready' => $isReady,
            'readiness_score' => round($readinessScore, 1),
            'filled_fields_count' => $filledFields,
            'total_fields_count' => count($businessPlanFields),
            'has_roadmap' => $hasRoadmapSteps,
            'missing_fields' => $missingFields,
            'recommendations' => $this->getRecommendations($filledFields, $hasRoadmapSteps, $missingFields)
        ], 200);
    }

    /**
     * Get recommendations based on readiness
     */
    private function getRecommendations(int $filledFields, bool $hasRoadmap, array $missingFields): array
    {
        $recommendations = [];

        if ($filledFields < 5) {
            $recommendations[] = 'Complete at least 5 business plan fields (business name, industry, business idea, target market, etc.)';
        }

        if (!$hasRoadmap) {
            $recommendations[] = 'Create a roadmap with at least one step';
        }

        if (in_array('business_idea', $missingFields)) {
            $recommendations[] = 'Describe your business idea in detail';
        }

        if (in_array('target_market_groups', $missingFields)) {
            $recommendations[] = 'Define your target market and customers';
        }

        return $recommendations;
    }

    /**
     * Get all meetings for the current user
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $meetings = Meeting::where('user_id', $user->id)
            ->with('advisor:id,name,email,specialization,title,bio')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json([
            'meetings' => $meetings
        ], 200);
    }

    /**
     * Store a new meeting
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'advisor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'topic' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        // Verify advisor exists and is actually an advisor
        $advisor = User::find($request->advisor_id);
        if (!$advisor || $advisor->role !== 'advisor') {
            return response()->json([
                'error' => 'Invalid advisor selected'
            ], 422);
        }

        $meeting = Meeting::create([
            'user_id' => $user->id,
            'advisor_id' => $request->advisor_id,
            'scheduled_at' => $request->scheduled_at,
            'topic' => $request->topic,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        $meeting->load('advisor:id,name,email,specialization,title,bio');

        return response()->json([
            'message' => 'Meeting scheduled successfully',
            'meeting' => $meeting
        ], 201);
    }
}
