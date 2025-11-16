<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove specific advisor accounts by email
        $emailsToRemove = [
            'vvesasusuri@gmail.com',
            'vesasusuri@gmail.com',
            'drinor.shala@digitalschool.tech',
        ];

        // Get the user IDs first
        $userIds = User::where('role', 'advisor')
            ->whereIn('email', $emailsToRemove)
            ->pluck('id');

        if ($userIds->isEmpty()) {
            return; // No users found to delete
        }

        // Disable foreign key checks temporarily (SQLite)
        DB::statement('PRAGMA foreign_keys = OFF;');

        try {
            // Delete related records from tables with foreign keys
            DB::table('advisor_notes')->whereIn('advisor_id', $userIds)->delete();
            DB::table('network_matches')->whereIn('user_id', $userIds)->orWhereIn('company_id', $userIds)->delete();
            DB::table('meeting_requests')->whereIn('user_id', $userIds)->delete();
            DB::table('sessions')->whereIn('user_id', $userIds)->delete();
            
            // Delete the users
            User::whereIn('id', $userIds)->delete();
        } finally {
            // Re-enable foreign key checks
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as we don't have the original data
        // If needed, advisors can be re-added manually
    }
};
