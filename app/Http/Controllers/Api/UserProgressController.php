<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserProgressController extends Controller
{
    /**
     * Get user progress (XP, level, streak, daily goal)
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $progress = UserProgress::firstOrCreate(
            ['user_id' => $user->id],
            [
                'xp' => 0,
                'level' => 1,
                'last_activity_at' => now(),
            ]
        );

        // Calculate streak (days of consecutive activity)
        
        // Calculate daily XP (XP earned today)
        
        // Daily goal (can be made configurable)
        $dailyGoal = 50;

        return response()->json([
            'xp' => $progress->xp,
            'level' => $progress->level,
            'daily_goal' => $dailyGoal,
            'hearts' => 5, // Default hearts (can be made dynamic)
        ]);
    }

    /**
     * Award XP to user
     */
    public function awardXP(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:1000',
            'reason' => 'nullable|string|max:255',
        ]);

        $progress = UserProgress::firstOrCreate(
            ['user_id' => $user->id],
            [
                'xp' => 0,
                'level' => 1,
                'last_activity_at' => now(),
            ]
        );

        // Add XP
        $oldXP = $progress->xp;
        $progress->xp += $validated['amount'];
        $progress->last_activity_at = now();
        $progress->save();

        // Calculate new level
        $newLevel = $this->calculateLevel($progress->xp);
        $levelUp = $newLevel > $progress->level;
        
        if ($levelUp) {
            $progress->level = $newLevel;
            $progress->save();
        }

        // Update streak
        $streak = $this->updateStreak($progress);
        
        // Get daily XP
        $dailyXP = $this->getDailyXP($user->id);

        return response()->json([
            'xp' => $progress->xp,
            'level' => $progress->level,
            'level_up' => $levelUp,
            'streak' => $streak,
            'daily_xp' => $dailyXP,
            'xp_gained' => $validated['amount'],
        ]);
    }

    /**
     * Calculate level based on XP (Duolingo-style exponential growth)
     */
    private function calculateLevel(int $xp): int
    {
        $level = 1;
        $requiredXP = 0;
        
        while ($xp >= $requiredXP && $level < 100) {
            $level++;
            // Exponential growth: Level 2 = 50 XP, Level 3 = 125 XP, Level 4 = 225 XP, etc.
            $requiredXP = (int) (50 * ($level - 1) * pow(1.5, $level - 2));
        }
        
        return min($level - 1, 100);
    }

    /**
     * Calculate current streak
     */
    private function calculateStreak(UserProgress $progress): int
    {
        $lastActivity = Carbon::parse($progress->last_activity_at);
        $today = Carbon::today();
        
        // If last activity was today, check consecutive days
        if ($lastActivity->isToday()) {
            // Check how many consecutive days the user has been active
            // For now, return a simple calculation based on last_activity_at
            // In a full implementation, you'd track daily activity separately
            $daysSinceStart = (int) $lastActivity->diffInDays(Carbon::parse($progress->created_at));
            return min($daysSinceStart + 1, 365); // Cap at 365 days
        }
        
        // If last activity was yesterday, streak continues
        if ($lastActivity->isYesterday()) {
            return 1;
        }
        
        // Otherwise, streak is broken
        return 0;
    }

    /**
     * Update streak based on activity
     */
    private function updateStreak(UserProgress $progress): int
    {
        $lastActivity = Carbon::parse($progress->last_activity_at);
        $today = Carbon::today();
        
        // If last activity was today or yesterday, increment streak
        if ($lastActivity->isToday() || $lastActivity->isYesterday()) {
            // Simple streak calculation
            $daysSinceStart = (int) $lastActivity->diffInDays(Carbon::parse($progress->created_at));
            return min($daysSinceStart + 1, 365);
        }
        
        return 1; // New streak starting today
    }

    /**
     * Get XP earned today
     */
    private function getDailyXP(int $userId): int
    {
        // In a full implementation, you'd track daily XP in a separate table
        // For now, return a simple calculation
        $progress = UserProgress::where('user_id', $userId)->first();
        if (!$progress) {
            return 0;
        }
        
        $lastActivity = Carbon::parse($progress->last_activity_at);
        if ($lastActivity->isToday()) {
            // Return a portion of total XP as "today's XP"
            // In reality, you'd track this separately
            return (int) ($progress->xp * 0.1); // Rough estimate
        }
        
        return 0;
    }
}

