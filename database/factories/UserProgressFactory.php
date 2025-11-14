<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProgress;

class UserProgressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProgress::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'xp' => fake()->numberBetween(-10000, 10000),
            'level' => fake()->numberBetween(-10000, 10000),
            'last_activity_at' => fake()->dateTime(),
        ];
    }
}
