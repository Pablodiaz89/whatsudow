<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Availability;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{

    protected $model = Availability::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month')->format('d/m/Y');
        $endDate = Carbon::createFromFormat('d/m/Y', $startDate)
            ->addHours($this->faker->numberBetween(1, 12))
            ->format('d/m/Y');

        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'title' => $this->faker->sentence,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['disponible', 'pre-reservado', 'no disponible']),
        ];
    }
}
