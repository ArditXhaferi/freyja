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

    /**
     * Generate a roadmap based on the user's business plan
     */
    public function generate(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get user's business plan data
        $businessName = $user->business_name ?? $user->company_planned_name ?? 'Your Startup';
        $industry = $user->industry ?? 'General';
        $isNewcomer = $user->is_newcomer_to_finland ?? false;
        $hasResidencePermit = $user->has_residence_permit ?? false;
        $isEuResident = $user->is_eu_resident ?? false;

        // Generate roadmap steps based on business plan
        $steps = [];
        $order = 1;

        // Step 1: Business Registration (if not established)
        if (!$user->year_of_establishment) {
            $steps[] = [
                'id' => $order,
                'title' => 'Register your business with the Trade Register',
                'description' => 'Register your company with the Finnish Trade Register (YTJ). This is required for all businesses operating in Finland. You can do this online at ytj.fi.',
                'status' => 'pending',
                'order' => $order++,
                'resources' => [
                    [
                        'title' => 'Finnish Trade Register (YTJ)',
                        'url' => 'https://www.ytj.fi/en/',
                        'description' => 'Official website for business registration in Finland'
                    ]
                ]
            ];
        }

        // Step 2: Residence Permit (if needed)
        if ($isNewcomer && !$hasResidencePermit && !$isEuResident) {
            $steps[] = [
                'id' => $order,
                'title' => 'Apply for residence permit for entrepreneurs',
                'description' => 'If you are not an EU/EEA citizen, you need to apply for a residence permit for entrepreneurs through Migri. This is required before you can start your business operations.',
                'status' => 'pending',
                'order' => $order++,
                'resources' => [
                    [
                        'title' => 'Migri - Residence Permit for Entrepreneurs',
                        'url' => 'https://migri.fi/en/residence-permit-for-an-entrepreneur',
                        'description' => 'Official information about residence permits for entrepreneurs'
                    ]
                ]
            ];
        }

        // Step 3: Business Bank Account
        $steps[] = [
            'id' => $order,
            'title' => 'Open a business bank account',
            'description' => 'Set up a separate business bank account for your company. This is required for business operations and helps keep personal and business finances separate.',
            'status' => 'pending',
            'order' => $order++,
            'resources' => [
                [
                    'title' => 'Nordea Business Banking',
                    'url' => 'https://www.nordea.fi/en/business/',
                    'description' => 'Business banking services'
                ],
                [
                    'title' => 'OP Business Banking',
                    'url' => 'https://www.op.fi/en/business/',
                    'description' => 'Business banking services'
                ]
            ]
        ];

        // Step 4: Business Plan Development
        if (!$user->business_idea) {
            $steps[] = [
                'id' => $order,
                'title' => 'Develop your business idea and plan',
                'description' => 'Create a comprehensive business plan that outlines your business idea, target market, products/services, and financial projections. This will help you clarify your vision and attract investors or funding.',
                'status' => 'pending',
                'order' => $order++,
                'resources' => []
            ];
        }

        // Step 5: Market Research
        if (!$user->target_market_groups) {
            $steps[] = [
                'id' => $order,
                'title' => 'Conduct market research',
                'description' => 'Research your target market, competitors, and industry trends. Understand your customers\' needs and preferences to position your business effectively.',
                'status' => 'pending',
                'order' => $order++,
                'resources' => [
                    [
                        'title' => 'Statistics Finland',
                        'url' => 'https://www.stat.fi/index_en.html',
                        'description' => 'Official statistics and market data for Finland'
                    ]
                ]
            ];
        }

        // Step 6: Funding (if needed)
        $steps[] = [
            'id' => $order,
            'title' => 'Explore funding options',
            'description' => 'Research and apply for startup funding, grants, or loans. Finland offers various funding options for entrepreneurs including Business Finland grants, startup loans, and angel investors.',
            'status' => 'pending',
            'order' => $order++,
            'resources' => [
                [
                    'title' => 'Business Finland',
                    'url' => 'https://www.businessfinland.fi/',
                    'description' => 'Finnish government funding and support for businesses'
                ],
                [
                    'title' => 'Finnvera',
                    'url' => 'https://www.finnvera.fi/eng',
                    'description' => 'Finnish state-owned financing company'
                ]
            ]
        ];

        // Step 7: Legal and Permits
        if (!$user->permits_notices) {
            $steps[] = [
                'id' => $order,
                'title' => 'Identify and apply for necessary permits',
                'description' => 'Determine what permits, licenses, or notifications your business needs based on your industry and activities. Some businesses require specific permits before starting operations.',
                'status' => 'pending',
                'order' => $order++,
                'resources' => [
                    [
                        'title' => 'Suomi.fi - Permits and Notifications',
                        'url' => 'https://www.suomi.fi/',
                        'description' => 'Centralized portal for permits and notifications'
                    ]
                ]
            ];
        }

        // Step 8: Insurance
        if (!$user->insurance_contracts) {
            $steps[] = [
                'id' => $order,
                'title' => 'Get business insurance',
                'description' => 'Obtain necessary business insurance including liability insurance, which is often required. Consider also property insurance, professional indemnity insurance, and other relevant coverage.',
                'status' => 'pending',
                'order' => $order++,
                'resources' => []
            ];
        }

        // Create or update roadmap
        $roadmap = Roadmap::firstOrNew(['user_id' => $user->id]);
        $roadmap->title = $businessName . ' Roadmap';
        $roadmap->roadmap_json = [
            'title' => $businessName . ' Roadmap',
            'steps' => $steps
        ];
        $roadmap->updated_by_ai_at = now();
        $roadmap->save();

        return response()->json([
            'message' => 'Roadmap generated successfully',
            'roadmap' => [
                'id' => $roadmap->id,
                'title' => $roadmap->title,
                'roadmap_json' => $roadmap->roadmap_json,
                'updated_by_ai_at' => $roadmap->updated_by_ai_at,
            ]
        ], 200);
    }
}

