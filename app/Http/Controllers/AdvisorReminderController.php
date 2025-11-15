<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Services\ReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function abort;
use function now;

class AdvisorReminderController extends Controller
{
    public function __construct(private ReminderService $reminderService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized');
        }

        $this->reminderService->generateForAdvisor($advisor);

        $reminders = $this->reminderService
            ->getActiveReminders($advisor)
            ->map(fn (Reminder $reminder) => [
                'id' => $reminder->id,
                'message' => $reminder->message,
                'remind_at' => $reminder->remind_at?->toIso8601String(),
                'meeting_id' => $reminder->meeting_id,
                'type' => $reminder->reminder_type,
            ]);

        return response()->json([
            'reminders' => $reminders,
        ]);
    }

    public function acknowledge(Request $request): JsonResponse
    {
        $advisor = Auth::user();

        if (!$advisor) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:reminders,id',
        ]);

        Reminder::where('advisor_id', $advisor->id)
            ->whereIn('id', $data['ids'])
            ->update([
                'reminder_active' => false,
                'seen_at' => now(),
            ]);

        return response()->json(['message' => 'Reminders acknowledged.']);
    }
}


