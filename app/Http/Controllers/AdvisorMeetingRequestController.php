<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingPrep;
use App\Models\MeetingRequest;
use App\Models\Roadmap;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use function abort;
use function collect;
use function now;

class AdvisorMeetingRequestController extends Controller
{
    public function index(): Response
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized');
        }

        MeetingRequest::whereNotNull('preferred_date')
            ->where('preferred_date', '<', now()->toDateString())
            ->delete();

        $pending = MeetingRequest::with('user')
            ->where('advisor_id', $advisor->id)
            ->latest()
            ->get()
            ->map(function (MeetingRequest $request) {
                $meetingPrep = MeetingPrep::where('user_id', $request->user_id)
                    ->latest('submitted_at')
                    ->first();
                $roadmaps = Roadmap::where('user_id', $request->user_id)->get();

                return [
                    'id' => $request->id,
                    'founder' => $request->user?->name ?? 'Unknown founder',
                    'message' => $request->message,
                    'preferred_date' => $request->preferred_date?->toDateString(),
                    'preferred_time' => $request->preferred_time,
                    'status' => $request->status,
                    'business' => [
                        'name' => $meetingPrep?->business_stage
                            ? ucfirst($meetingPrep->business_stage) . ' venture'
                            : ($request->user?->name ?? 'Business'),
                        'stage' => $meetingPrep?->business_stage,
                        'summary' => $meetingPrep?->business_summary,
                        'targets' => $meetingPrep?->target_customers,
                        'questions' => $meetingPrep?->questions_for_advisor,
                    ],
                    'roadmaps' => $roadmaps
                        ->map(fn (Roadmap $roadmap) => $this->formatRoadmap($roadmap))
                        ->filter()
                        ->values(),
                ];
            });

        return Inertia::render('AdvisorMeetingRequests', [
            'advisor' => [
                'name' => $advisor->name,
                'initials' => collect(explode(' ', $advisor->name))
                    ->map(fn ($segment) => mb_substr($segment, 0, 1))
                    ->join(''),
            ],
            'requests' => $pending,
        ]);
    }
    public function accept(Request $request, MeetingRequest $meetingRequest): RedirectResponse
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized');
        }

        if ((int) $meetingRequest->advisor_id !== $advisor->id) {
            abort(403, 'Not allowed');
        }

        $validated = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string|max:2000',
        ]);

        $scheduledAt = Carbon::parse(
            "{$validated['scheduled_date']} {$validated['scheduled_time']}",
            config('app.timezone')
        );

        DB::transaction(function () use ($meetingRequest, $advisor, $scheduledAt, $validated): void {
            Meeting::create([
                'user_id' => $meetingRequest->user_id,
                'advisor_id' => $advisor->id,
                'meeting_request_id' => $meetingRequest->id,
                'scheduled_at' => $scheduledAt,
                'location' => $validated['location'] ?? null,
                'agenda' => $validated['agenda'] ?? $meetingRequest->message,
            ]);

            $meetingRequest->status = 'accepted';
            $meetingRequest->save();
            $meetingRequest->delete();
        });

        return back()->with('success', 'Meeting scheduled successfully.');
    }

    public function reject(MeetingRequest $meetingRequest): RedirectResponse
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized');
        }

        if ((int) $meetingRequest->advisor_id !== $advisor->id) {
            abort(403, 'Not allowed');
        }

        $meetingRequest->status = 'rejected';
        $meetingRequest->save();

        return back()->with('success', 'Request rejected.');
    }
    private function formatRoadmap(?Roadmap $roadmap): ?array
    {
        if (!$roadmap || !$roadmap->roadmap_json || empty($roadmap->roadmap_json['steps'] ?? [])) {
            return null;
        }

        $steps = collect($roadmap->roadmap_json['steps'])
            ->values()
            ->map(function ($step, $index) {
                return [
                    'id' => (string)($step['id'] ?? $index + 1),
                    'title' => $step['title'] ?? 'Untitled step',
                    'description' => $step['description'] ?? null,
                    'status' => $step['status'] ?? 'pending',
                    'order' => $step['order'] ?? $index,
                ];
            });

        return [
            'title' => $roadmap->title ?? 'Founder roadmap',
            'steps' => $steps,
        ];
    }
}


