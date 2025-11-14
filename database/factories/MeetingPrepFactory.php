<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\MeetingPrep;
use App\Models\User;

class MeetingPrepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MeetingPrep::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'business_summary' => fake()->text(),
            'target_customers' => fake()->text(),
            'questions_for_advisor' => fake()->text(),
            'business_stage' => fake()->randomElement(["idea","planning","launched"]),
            'generated_pdf_path' => fake()->word(),
            'submitted_at' => fake()->dateTime(),
        ];
    }
}
