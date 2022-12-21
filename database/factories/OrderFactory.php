<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-7 days');
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'billing_email' => fake()->email(),
            'billing_name' => fake()->name(),
            'billing_address' =>fake()->address(),
            'billing_city' => fake()->city(),
            'billing_state' => fake()->country(),
            'billing_postal_code' => fake()->countryCode(),
            'billing_phone' => fake()->phoneNumber(),
            'billing_total' => fake()->numberBetween(1000,10000),
            'created_at' => $date,
        ];
    }
}
