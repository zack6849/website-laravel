<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename =  $this->faker->asciify('**********') . $this->faker->fileExtension();
        return [
            'user_id' => fn() => User::factory()->create()->id,
            'file_location' => "storage/app/$filename",
            'original_filename' => $filename,
            'mime' => $this->faker->mimeType(),
            'filename' => $filename,
        ];
    }
}
