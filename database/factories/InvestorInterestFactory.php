<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Investor;
use App\Models\InvestorInterest;
use App\Models\User;

class InvestorInterestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvestorInterest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'investor_id' => Investor::factory(),
            'message' => fake()->text(),
            'status' => fake()->randomElement(["pending","accepted","rejected"]),
        ];
    }
}
