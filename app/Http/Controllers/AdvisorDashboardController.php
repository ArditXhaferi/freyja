<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InvestorInterest;
use App\Models\Meeting;
use App\Models\MeetingPrep;
use App\Models\MeetingRequest;
use App\Models\VoiceSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use function abort;
use function collect;
use function now;

class AdvisorDashboardController extends Controller
{
    /**
     * Display the advisor dashboard.
     */
    public function __invoke(Request $request): Response
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized.');
        }

        $meetingLeads = MeetingPrep::with('user')
            ->latest('submitted_at')
            ->take(6)
            ->get()
            ->map(fn (MeetingPrep $prep) => [
                'id' => $prep->id,
                'founder' => $prep->user?->name ?? 'Unknown founder',
                'stage' => $prep->business_stage,
                'submitted_at' => optional($prep->submitted_at)->toIso8601String(),
                'business_summary' => Str::limit($prep->business_summary, 140),
                'questions' => Str::limit($prep->questions_for_advisor, 100),
            ]);

        $interestRequests = InvestorInterest::with(['user', 'investor'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (InvestorInterest $interest) => [
                'id' => $interest->id,
                'founder' => $interest->user?->name ?? 'Unknown founder',
                'investor' => $interest->investor?->name ?? 'Unknown investor',
                'status' => $interest->status,
                'message' => Str::limit($interest->message, 120),
                'created_at' => $interest->created_at?->toIso8601String(),
            ]);

        $recentSessions = VoiceSession::with('user')
            ->latest('started_at')
            ->take(5)
            ->get()
            ->map(fn (VoiceSession $session) => [
                'id' => $session->id,
                'founder' => $session->user?->name ?? 'Unknown founder',
                'status' => $session->status,
                'started_at' => optional($session->started_at)->toIso8601String(),
                'completed_at' => optional($session->completed_at)->toIso8601String(),
                'model' => $session->model,
            ]);

        $stats = [
            'activeLeads' => MeetingPrep::whereNotNull('submitted_at')->count(),
            'pendingIntros' => InvestorInterest::where('status', 'pending')->count(),
            'voiceSessionsToday' => VoiceSession::whereDate('started_at', now()->toDateString())->count(),
        ];

        MeetingRequest::whereNotNull('preferred_date')
            ->where('preferred_date', '<', now()->toDateString())
            ->delete();

        $meetingRequests = MeetingRequest::with('user')
            ->where('advisor_id', $advisor->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (MeetingRequest $request) => [
                'id' => $request->id,
                'founder' => $request->user?->name ?? 'Unknown founder',
                'message' => Str::limit($request->message, 140),
                'preferred_date' => $request->preferred_date?->toDateString(),
                'preferred_time' => $request->preferred_time
                    ? Carbon::parse($request->preferred_time)->format('H:i')
                    : null,
            ]);

        $upcomingMeetings = Meeting::with('user')
            ->where('advisor_id', $advisor->id)
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(fn (Meeting $meeting) => [
                'id' => $meeting->id,
                'founder' => $meeting->user?->name ?? 'Unknown founder',
                'scheduled_at' => $meeting->scheduled_at?->toIso8601String(),
                'status' => $meeting->status,
                'location' => $meeting->location,
            ]);

        $dayLabels = collect(['S', 'M', 'T', 'W', 'T', 'F', 'S']);
        $trendData = MeetingPrep::whereNotNull('submitted_at')
            ->get()
            ->groupBy(fn (MeetingPrep $prep) => optional($prep->submitted_at)->format('N') ?? '1');

        $analytics = $dayLabels->map(function ($label, $index) use ($trendData) {
            $dayKey = (string) ($index + 1);
            $count = optional($trendData->get($dayKey))->count() ?? 0;

            return [
                'label' => $label,
                'count' => $count,
                'percentage' => min(100, $count * 20),
            ];
        });

        $teamCollab = $meetingLeads->map(function ($lead) {
            return [
                'name' => $lead['founder'],
                'topic' => ucfirst($lead['stage']) . ' stage review',
                'status' => match ($lead['stage']) {
                    'idea' => 'Planning',
                    'planning' => 'In Progress',
                    'launched' => 'Launched',
                    default => 'Pending',
                },
            ];
        });

        $projectList = $interestRequests->map(function ($request) {
            $date = $request['created_at'] ? Carbon::parse($request['created_at'])->format('M d, Y') : null;

            return [
                'title' => "Intro to {$request['investor']}",
                'due' => $date,
                'status' => ucfirst($request['status']),
            ];
        });

        $latestMeeting = $meetingLeads->first();
        $latestSession = VoiceSession::latest('started_at')->first();

        $progress = [
            'completed' => InvestorInterest::where('status', 'accepted')->count(),
            'in_progress' => $stats['pendingIntros'],
            'pending' => $meetingLeads->count(),
        ];
    

        $timeTracker = [
            'duration' => $latestSession && $latestSession->completed_at && $latestSession->started_at
                ? $latestSession->completed_at->diffAsCarbonInterval($latestSession->started_at)->cascade()->forHumans([
                    'short' => true,
                    'join' => true,
                ])
                : '00:00:00',
            'status' => $latestSession?->status ?? 'pending',
        ];

        return Inertia::render('AdvisorDashboard', [
            'advisor' => [
                'name' => $advisor->name,
                'initials' => collect(explode(' ', $advisor->name))
                    ->map(fn ($segment) => mb_substr($segment, 0, 1))
                    ->join(''),
            ],
            'hero' => [
                'title' => 'Espoo Business Advisor',
                'tagline' => 'Your AI companion for business success in Finland',
                'message' => "Feel free to start reviewing leads whenever you're ready. I'm here to listen and help.",
            ],
            'stats' => $stats,
            'meetingLeads' => $meetingLeads,
            'interestRequests' => $interestRequests,
            'recentSessions' => $recentSessions,
            'analytics' => $analytics,
            'teamCollab' => $teamCollab,
            'projectList' => $projectList,
            'latestMeeting' => $latestMeeting,
            'progress' => $progress,
            'timeTracker' => $timeTracker,
            'meetingRequests' => $meetingRequests,
            'upcomingMeetings' => $upcomingMeetings,
        ]);
    }
}


