<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\Roadmap;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/voice-roadmap', function () {
        $user = Auth::user();
        
        // Fetch roadmap
        $roadmap = Roadmap::where('user_id', $user->id)->first();
        $roadmapData = $roadmap ? $roadmap->roadmap_json : null;
        
        // Fetch business plan data (all fields from user including contextual fields)
        $businessPlanData = [
            // Contextual fields
            'country_of_origin' => $user->country_of_origin,
            'is_eu_resident' => $user->is_eu_resident,
            'is_newcomer_to_finland' => $user->is_newcomer_to_finland,
            'has_residence_permit' => $user->has_residence_permit,
            'residence_permit_type' => $user->residence_permit_type,
            'years_in_finland' => $user->years_in_finland,
            'has_business_experience' => $user->has_business_experience,
            'language' => $user->language,
            // Business plan fields
            'business_name' => $user->business_name,
            'company_contact_info' => $user->company_contact_info,
            'industry' => $user->industry,
            'company_planned_name' => $user->company_planned_name,
            'company_type' => $user->company_type,
            'address' => $user->address,
            'zip_code' => $user->zip_code,
            'postal_district' => $user->postal_district,
            'year_of_establishment' => $user->year_of_establishment,
            'number_of_employees' => $user->number_of_employees,
            'internet_address' => $user->internet_address,
            'business_id' => $user->business_id,
            'company_owners_holdings' => $user->company_owners_holdings,
            'business_idea' => $user->business_idea,
            'competence_skills' => $user->competence_skills,
            'swot_analysis' => $user->swot_analysis,
            'products_services_general' => $user->products_services_general,
            'products_services_detailed' => $user->products_services_detailed,
            'sales_marketing' => $user->sales_marketing,
            'production_logistics' => $user->production_logistics,
            'distribution_network' => $user->distribution_network,
            'target_market_groups' => $user->target_market_groups,
            'competitors' => $user->competitors,
            'competitive_situation' => $user->competitive_situation,
            'third_parties_partners' => $user->third_parties_partners,
            'operating_environment_risks' => $user->operating_environment_risks,
            'vision_long_term' => $user->vision_long_term,
            'industry_future_prospects' => $user->industry_future_prospects,
            'permits_notices' => $user->permits_notices,
            'insurance_contracts' => $user->insurance_contracts,
            'intellectual_property_rights' => $user->intellectual_property_rights,
            'support_network' => $user->support_network,
            'my_business_comprehensive' => $user->my_business_comprehensive,
        ];
        
        return Inertia::render('VoiceRoadmap', [
            'initialRoadmap' => $roadmapData,
            'initialBusinessPlan' => $businessPlanData,
            'userName' => $user->name, // Pass user name for first message
        ]);
    })->name('voice-roadmap');
});
