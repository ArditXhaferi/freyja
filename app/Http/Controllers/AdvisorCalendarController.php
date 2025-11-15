<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use function abort;
use function collect;

class AdvisorCalendarController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized.');
        }

        $meetings = Meeting::with('user')
            ->where('advisor_id', $advisor->id)
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn (Meeting $meeting) => [
                'id' => $meeting->id,
                'founder' => $meeting->user?->name ?? 'Unknown founder',
                'scheduled_at' => $meeting->scheduled_at?->toIso8601String(),
                'agenda' => Str::limit($meeting->agenda, 140),
                'location' => $meeting->location,
            ]);

        return Inertia::render('AdvisorCalendar', [
            'advisor' => [
                'name' => $advisor->name,
                'initials' => collect(explode(' ', $advisor->name))
                    ->map(fn ($segment) => mb_substr($segment, 0, 1))
                    ->join(''),
            ],
            'meetings' => $meetings,
        ]);
    }
}


