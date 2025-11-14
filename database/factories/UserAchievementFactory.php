<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;

class UserAchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAchievement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'achievement_id' => Achievement::factory(),
            'earned_at' => fake()->dateTime(),
        ];
    }
}
