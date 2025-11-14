<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Investor;

class InvestorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Investor::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->company(),
            'bio' => fake()->text(),
            'linkedin_url' => fake()->word(),
            'focus_areas' => '{}',
            'accepting_pitches' => fake()->boolean(),
        ];
    }
}
