<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_eu_resident')->nullable()->after('country_of_origin');
            $table->boolean('is_newcomer_to_finland')->nullable()->after('is_eu_resident');
            $table->boolean('has_residence_permit')->nullable()->after('is_newcomer_to_finland');
            $table->string('residence_permit_type')->nullable()->after('has_residence_permit');
            $table->integer('years_in_finland')->nullable()->after('residence_permit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_eu_resident',
                'is_newcomer_to_finland',
                'has_residence_permit',
                'residence_permit_type',
                'years_in_finland'
            ]);
        });
    }
};
