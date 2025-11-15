<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdvisorNetworkController extends Controller
{
    public function __invoke(Request $request): Response
    {
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

        return Inertia::render('AdvisorNetwork', [
            'advisor' => [
                'name' => $request->user()->name,
            ],
            'companies' => $companies,
        ]);
    }
}

