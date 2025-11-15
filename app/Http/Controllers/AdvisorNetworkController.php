<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NetworkMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdvisorNetworkController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $advisor = $request->user();

        $companies = User::query()
            ->where('role', 'entrepreneur')
            ->with(['meetingPreps' => function ($query) {
                $query->latest();
            }])
            ->get()
            ->map(function (User $user) {
                $latestPrep = $user->meetingPreps->first();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company' => $user->business_name ?? "{$user->name}'s Company",
                    'summary' => $latestPrep?->business_summary ?? 'This founder hasn’t shared a summary yet.',
                    'questions' => $latestPrep?->questions_for_advisor,
                    'stage' => $latestPrep?->business_stage ?? 'Not specified',
                    'target_customers' => $latestPrep?->target_customers,
                    'updated_at' => optional($latestPrep?->submitted_at ?? $user->updated_at)->toIso8601String(),
                    'country' => $user->country_of_origin,
                    'language' => $user->language,
                ];
            });

        // Build simple graph data from network_matches table
        $matches = NetworkMatch::query()
            ->where(function ($q) use ($advisor) {
                $q->where('user_id', $advisor->id)
                    ->orWhere('company_id', $advisor->id);
            })
            ->whereIn('action', ['like', 'super_like'])
            ->get();

        $edges = $matches->map(function (NetworkMatch $match) use ($advisor) {
            $isAdvisorSource = $match->user_id === $advisor->id;
            $otherId = $isAdvisorSource ? $match->company_id : $match->user_id;

            $strength = $match->action === 'super_like' ? 1.0 : 0.7;
            if ($match->is_mutual) {
                $strength += 0.3;
            }

            return [
                'id' => "edge-{$match->id}",
                'from' => (string) $advisor->id,
                'to' => (string) $otherId,
                'strength' => min($strength, 1.5),
            ];
        });

        return Inertia::render('AdvisorNetwork', [
            'advisor' => [
                'id' => $advisor->id,
                'name' => $advisor->name,
            ],
            'companies' => $companies,
            'networkEdges' => $edges,
        ]);
    }
}

