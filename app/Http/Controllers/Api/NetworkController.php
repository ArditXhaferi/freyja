<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkMatch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NetworkController extends Controller
{
    /**
     * Get companies available for networking (exclude current user and already swiped)
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        // Get IDs of companies the user has already interacted with
        $swipedCompanyIds = NetworkMatch::where('user_id', $user->id)
            ->pluck('company_id')
            ->toArray();
        
        // Get companies with complete business info (role = 'client' and has business data)
        $companies = User::where('role', 'client')
            ->where('id', '!=', $user->id)
            ->whereNotNull('business_name')
            ->whereNotNull('industry')
            ->whereNotIn('id', $swipedCompanyIds)
            ->select(
                'id',
                'name',
                'business_name',
                'industry',
                'company_type',
                'year_of_establishment',
                'number_of_employees',
                'internet_address',
                'address',
                'postal_district',
                'business_idea',
                'products_services_general',
                'bio',
                'specialization'
            )
            ->get();
        
        return response()->json([
            'companies' => $companies
        ], 200);
    }

    /**
     * Handle swipe action (like, pass, super_like)
     */
    public function swipe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:users,id',
            'action' => 'required|in:like,pass,super_like',
        ]);

        $user = Auth::user();
        $companyId = (int) $validated['company_id'];
        $action = $validated['action'];
        
        Log::info('Swipe attempt', [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'action' => $action,
            'request_data' => $request->all()
        ]);

        // Prevent swiping on yourself
        if ($user->id === (int) $companyId) {
            return response()->json([
                'message' => 'You cannot swipe on your own company'
            ], 422);
        }

        // Check if already swiped
        $existingMatch = NetworkMatch::where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->first();

        if ($existingMatch) {
            return response()->json([
                'message' => 'You have already interacted with this company'
            ], 422);
        }

        // Check for mutual match if action is 'like' or 'super_like'
        $isMutual = false;
        if ($action === 'like' || $action === 'super_like') {
            $mutualMatch = NetworkMatch::where('user_id', $companyId)
                ->where('company_id', $user->id)
                ->whereIn('action', ['like', 'super_like'])
                ->first();

            if ($mutualMatch) {
                $isMutual = true;
                // Update the mutual match to mark it as mutual
                $mutualMatch->update(['is_mutual' => true]);
            }
        }

        try {
            // Create the match/swipe record
            $match = NetworkMatch::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'action' => $action,
                'is_mutual' => $isMutual,
            ]);

            Log::info('Match created successfully', [
                'match_id' => $match->id,
                'user_id' => $user->id,
                'company_id' => $companyId,
                'action' => $action,
                'is_mutual' => $isMutual
            ]);

            return response()->json([
                'match' => $match,
                'is_mutual' => $isMutual,
                'message' => $isMutual ? 'It\'s a match! 🎉' : 'Action recorded'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating network match: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'company_id' => $companyId,
                'action' => $action,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to save match. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's matches
     */
    public function matches(): JsonResponse
    {
        $user = Auth::user();

        // Get all companies the user has liked (not passed)
        $matches = NetworkMatch::where('user_id', $user->id)
            ->whereIn('action', ['like', 'super_like'])
            ->with(['company' => function ($query) {
                $query->select(
                    'id',
                    'name',
                    'business_name',
                    'industry',
                    'company_type',
                    'year_of_establishment',
                    'number_of_employees',
                    'internet_address',
                    'address',
                    'postal_district',
                    'business_idea',
                    'products_services_general',
                    'bio',
                    'specialization'
                );
            }])
            ->orderBy('is_mutual', 'desc') // Show mutual matches first
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'matches' => $matches
        ], 200);
    }

    /**
     * Get companies user has liked
     */
    public function liked(): JsonResponse
    {
        $user = Auth::user();

        $liked = NetworkMatch::where('user_id', $user->id)
            ->whereIn('action', ['like', 'super_like'])
            ->with(['company' => function ($query) {
                $query->select(
                    'id',
                    'name',
                    'business_name',
                    'industry',
                    'company_type',
                    'year_of_establishment',
                    'number_of_employees',
                    'internet_address',
                    'address',
                    'postal_district',
                    'business_idea',
                    'products_services_general',
                    'bio',
                    'specialization'
                );
            }])
            ->get();

        return response()->json([
            'liked' => $liked
        ], 200);
    }
}
