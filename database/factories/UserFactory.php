<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => fake()->password(),
            'role' => fake()->randomElement(["client","advisor","investor"]),
            'language' => fake()->word(),
            'country_of_origin' => fake()->word(),
            'has_business_experience' => fake()->boolean(),
            'onboarding_completed_at' => fake()->dateTime(),
            'email_verified_at' => fake()->dateTime(),
            'remember_token' => fake()->uuid(),
        ];
    }
}
