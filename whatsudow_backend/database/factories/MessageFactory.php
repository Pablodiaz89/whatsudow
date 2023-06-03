<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::all()->random()->id,
            'sender_name' => fake()->name,
            'sender_email' => fake()->unique()->safeEmail,
            'sender_telefono' => fake()->phoneNumber,
            'addresse_id' => User::all()->random()->id,
            'parent_id' => null, 
            'title' => fake()->sentence,
            'event_date' => fake()->date,
            'location_id' => Location::all()->random()->id,
            'description' => fake()->paragraph,
            'message' => fake()->text,
            'read' => false,
        ];
    }
}
