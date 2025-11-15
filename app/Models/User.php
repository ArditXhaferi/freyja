<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'specialization',
        'title',
        'bio',
        'languages',
        'language',
        'country_of_origin',
        'is_eu_resident',
        'is_newcomer_to_finland',
        'has_residence_permit',
        'residence_permit_type',
        'years_in_finland',
        'has_business_experience',
        'onboarding_completed_at',
        'email_verified_at',
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_eu_resident' => 'boolean',
            'is_newcomer_to_finland' => 'boolean',
            'has_residence_permit' => 'boolean',
            'years_in_finland' => 'integer',
            'has_business_experience' => 'boolean',
            'onboarding_completed_at' => 'timestamp',
            'email_verified_at' => 'timestamp',
            'year_of_establishment' => 'integer',
            'number_of_employees' => 'integer',
            'swot_analysis' => 'array',
            'products_services_detailed' => 'array',
            'languages' => 'array',
        ];
    }

    /**
     * Scope a query to only include advisors.
     */
    public function scopeAdvisors($query)
    {
        return $query->where('role', 'advisor');
    }

    /**
     * Scope a query to filter by specialization.
     */
    public function scopeBySpecialization($query, string $specialization)
    {
        return $query->where('specialization', $specialization);
    }
}
