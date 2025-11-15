<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Meeting;
use App\Models\Reminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReminderService
{
    private const THRESHOLDS = [
        'week' => '1 week',
        'three_days' => '3 days',
        'one_day' => '1 day',
        'one_hour' => '1 hour',
    ];

    public function generateForAdvisor(User $advisor): void
    {
        $meetings = Meeting::where('advisor_id', $advisor->id)
            ->whereBetween('scheduled_at', [now(), now()->copy()->addWeek()])
            ->get();

        foreach ($meetings as $meeting) {
            foreach (self::THRESHOLDS as $type => $interval) {
                $remindAt = Carbon::parse($meeting->scheduled_at)->sub($interval);

                if ($remindAt->lessThan(now())) {
                    continue;
                }

                Reminder::firstOrCreate(
                    [
                        'advisor_id' => $advisor->id,
                        'meeting_id' => $meeting->id,
                        'reminder_type' => $type,
                    ],
                    [
                        'message' => $this->buildMessage($meeting, $type),
                        'remind_at' => $remindAt,
                    ]
                );
            }
        }
    }

    public function getActiveReminders(User $advisor): Collection
    {
        return Reminder::where('advisor_id', $advisor->id)
            ->where('reminder_active', true)
            ->where('remind_at', '<=', now())
            ->orderBy('remind_at')
            ->get();
    }

    private function buildMessage(Meeting $meeting, string $type): string
    {
        $label = match ($type) {
            'week' => '1 week',
            'three_days' => '3 days',
            'one_day' => '1 day',
            'one_hour' => '1 hour',
            default => 'Soon',
        };

        return sprintf(
            'Meeting with %s starts in %s (%s).',
            $meeting->user?->name ?? 'founder',
            $label,
            Carbon::parse($meeting->scheduled_at)->format('M d, H:i')
        );
    }
}


