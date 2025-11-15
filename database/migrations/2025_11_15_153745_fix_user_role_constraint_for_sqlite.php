<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support modifying CHECK constraints, so we need to recreate the table
        if (DB::getDriverName() === 'sqlite') {
            // Check if constraint already allows 'entrepreneur'
            $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'")[0]->sql ?? '';
            
            if (strpos($createTable, "'entrepreneur'") !== false) {
                // Constraint already allows 'entrepreneur', just update any 'client' roles to 'entrepreneur'
                DB::table('users')
                    ->where('role', 'client')
                    ->update(['role' => 'entrepreneur']);
                return;
            }
            
            // Clean up any partial migration attempts
            DB::statement('DROP TABLE IF EXISTS users_new');
            
            // Replace the role constraint
            $newCreateTable = preg_replace(
                '/"role"[^,)]+CHECK\([^)]+\)/',
                '"role" varchar check ("role" in (\'entrepreneur\', \'advisor\', \'investor\')) not null default \'entrepreneur\'',
                $createTable
            );
            
            // Create new table with modified constraint
            $newCreateTable = str_replace('CREATE TABLE "users"', 'CREATE TABLE "users_new"', $newCreateTable);
            DB::statement($newCreateTable);

            // Copy all data, converting 'client' to 'entrepreneur' for the role column
            $columns = DB::select("PRAGMA table_info(users)");
            $columnNames = array_map(fn($col) => '"' . $col->name . '"', $columns);
            $selectColumns = array_map(function($col) {
                return $col === '"role"' 
                    ? 'CASE WHEN "role" = \'client\' THEN \'entrepreneur\' ELSE "role" END as "role"'
                    : $col;
            }, $columnNames);
            
            DB::statement('INSERT INTO "users_new" SELECT ' . implode(', ', $selectColumns) . ' FROM "users"');

            // Drop the old table
            Schema::dropIfExists('users');

            // Rename the new table
            DB::statement('ALTER TABLE "users_new" RENAME TO "users"');

            // Recreate indexes
            $indexes = DB::select("SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name='users' AND sql IS NOT NULL");
            foreach ($indexes as $index) {
                if ($index->sql && strpos($index->sql, 'users') !== false) {
                    DB::statement($index->sql);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Get the CREATE TABLE statement and modify it back to 'client'
            $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'")[0]->sql;
            
            // Replace the role constraint back to 'client'
            $newCreateTable = preg_replace(
                '/role[^,)]+CHECK\([^)]+\)/',
                'role VARCHAR(255) NOT NULL DEFAULT "client" CHECK(role IN ("client", "advisor", "investor"))',
                $createTable
            );
            
            // Create new table with modified constraint
            $newCreateTable = str_replace('CREATE TABLE users', 'CREATE TABLE users_new', $newCreateTable);
            DB::statement($newCreateTable);

            // Copy all data, converting 'entrepreneur' back to 'client' for the role column
            $columns = DB::select("PRAGMA table_info(users)");
            $columnNames = array_map(fn($col) => $col->name, $columns);
            $selectColumns = array_map(function($col) {
                return $col === 'role' 
                    ? 'CASE WHEN role = "entrepreneur" THEN "client" ELSE role END as role'
                    : $col;
            }, $columnNames);
            
            DB::statement('INSERT INTO users_new SELECT ' . implode(', ', $selectColumns) . ' FROM users');

            // Drop the old table
            Schema::dropIfExists('users');

            // Rename the new table
            DB::statement('ALTER TABLE users_new RENAME TO users');

            // Recreate indexes
            $indexes = DB::select("SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name='users' AND sql IS NOT NULL");
            foreach ($indexes as $index) {
                if ($index->sql) {
                    DB::statement($index->sql);
                }
            }
        }
    }
};
