<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MeetingRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use function abort_unless;

class MeetingRequestController extends Controller
{
    public function create(): Response
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $advisors = User::where('role', 'advisor')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        return Inertia::render('RequestMeeting', [
            'advisors' => $advisors,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $validated = $request->validate([
            'advisor_id' => 'required|exists:users,id',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:2000',
        ]);

        MeetingRequest::create([
            'user_id' => $user->id,
            'advisor_id' => $validated['advisor_id'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'message' => $validated['message'] ?? null,
        ]);

        return redirect()->route('advisor.dashboard')->with('success', 'Meeting request sent.');
    }
}


