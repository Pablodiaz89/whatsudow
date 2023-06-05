<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Pdf;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pdf>
 */
class PdfFactory extends Factory
{
    protected $model = Pdf::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'file_id' => File::factory()->create()->id,
        ];
    }
}

