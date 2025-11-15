<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvisorController extends Controller
{
    /**
     * Get all advisors with their specializations
     */
    public function index(): JsonResponse
    {
        $advisors = User::advisors()
            ->select('id', 'name', 'email', 'specialization', 'title', 'bio', 'languages')
            ->get();

        return response()->json([
            'advisors' => $advisors
        ], 200);
    }

    /**
     * Match advisors based on roadmap step content
     */
    public function matchByStep(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'step_title' => 'required|string',
            'step_description' => 'nullable|string',
        ]);

        $stepText = strtolower($validated['step_title'] . ' ' . ($validated['step_description'] ?? ''));
        
        // Map step content to specializations
        $specializationMap = [
            'residence_permit' => ['residence permit', 'residence', 'permit', 'migri', 'immigration'],
            'business_registration' => ['business registration', 'register', 'trade register', 'company registration', 'y-tunnus'],
            'tax' => ['tax', 'vat', 'accounting', 'verohallinto', 'tax office'],
            'funding' => ['funding', 'grant', 'investor', 'investment', 'finance', 'loan'],
            'legal' => ['legal', 'contract', 'law', 'intellectual property', 'ip', 'patent', 'trademark'],
            'marketing' => ['marketing', 'sales', 'branding', 'advertising', 'promotion'],
        ];

        $matchedSpecializations = [];
        foreach ($specializationMap as $specialization => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($stepText, $keyword)) {
                    $matchedSpecializations[] = $specialization;
                    break;
                }
            }
        }

        $advisors = [];
        if (!empty($matchedSpecializations)) {
            $advisors = User::advisors()
                ->whereIn('specialization', $matchedSpecializations)
                ->select('id', 'name', 'email', 'specialization', 'title', 'bio', 'languages')
                ->get();
        }

        return response()->json([
            'advisors' => $advisors,
            'matched_specializations' => array_unique($matchedSpecializations)
        ], 200);
    }
}
