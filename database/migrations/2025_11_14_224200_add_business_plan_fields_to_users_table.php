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
            $table->string('business_name')->nullable();
            $table->text('company_contact_info')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_planned_name')->nullable();
            $table->string('company_type')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('postal_district')->nullable();
            $table->integer('year_of_establishment')->nullable();
            $table->integer('number_of_employees')->nullable();
            $table->string('internet_address')->nullable();
            $table->string('business_id')->nullable();
            $table->text('company_owners_holdings')->nullable();
            $table->text('business_idea')->nullable();
            $table->text('competence_skills')->nullable();
            $table->json('swot_analysis')->nullable();
            $table->text('products_services_general')->nullable();
            $table->json('products_services_detailed')->nullable();
            $table->text('sales_marketing')->nullable();
            $table->text('production_logistics')->nullable();
            $table->text('distribution_network')->nullable();
            $table->text('target_market_groups')->nullable();
            $table->text('competitors')->nullable();
            $table->text('competitive_situation')->nullable();
            $table->text('third_parties_partners')->nullable();
            $table->text('operating_environment_risks')->nullable();
            $table->text('vision_long_term')->nullable();
            $table->text('industry_future_prospects')->nullable();
            $table->text('permits_notices')->nullable();
            $table->text('insurance_contracts')->nullable();
            $table->text('intellectual_property_rights')->nullable();
            $table->text('support_network')->nullable();
            $table->text('my_business_comprehensive')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'company_contact_info',
                'industry',
                'company_planned_name',
                'company_type',
                'address',
                'zip_code',
                'postal_district',
                'year_of_establishment',
                'number_of_employees',
                'internet_address',
                'business_id',
                'company_owners_holdings',
                'business_idea',
                'competence_skills',
                'swot_analysis',
                'products_services_general',
                'products_services_detailed',
                'sales_marketing',
                'production_logistics',
                'distribution_network',
                'target_market_groups',
                'competitors',
                'competitive_situation',
                'third_parties_partners',
                'operating_environment_risks',
                'vision_long_term',
                'industry_future_prospects',
                'permits_notices',
                'insurance_contracts',
                'intellectual_property_rights',
                'support_network',
                'my_business_comprehensive',
            ]);
        });
    }
};
