<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'icon' => fake()->sentence(1),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Category $category) {
            // Obtener un usuario existente
            $user = User::inRandomOrder()->first();

            // Asignar el usuario a la categoría
            $category->users()->attach($user->id);
        });
    }
}
