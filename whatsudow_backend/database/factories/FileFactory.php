<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{

    protected $model = File::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = $this->faker->word() . '.' . $this->faker->fileExtension();
        $path = UploadedFile::fake()->create('example_file.txt')->store('files');
        $type = $this->faker->randomElement(['image', 'pdf']);
        $user_id = random_int(1, 10);

        return [
            'filename' => $filename,
            'path' => $path,
            'type' => $type,
            'user_id' => $user_id,
        ];
    }
}
