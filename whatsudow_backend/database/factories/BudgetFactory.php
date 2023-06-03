<?php

namespace Database\Factories;



use App\Models\Budget;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BudgetFactory extends Factory
{
    protected $model = Budget::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'title' => $this->faker->sentence(),
            'event_date' => $this->faker->date(),
            'location_id' => Location::all()->random()->id,
            'description' => $this->faker->paragraph(),
        ];
    }
}
