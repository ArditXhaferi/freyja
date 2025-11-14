<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Roadmap;
use App\Models\User;

class RoadmapFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Roadmap::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'roadmap_json' => '{}',
            'updated_by_ai_at' => fake()->dateTime(),
            'is_locked' => fake()->boolean(),
        ];
    }
}
