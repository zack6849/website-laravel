<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileUploadTest extends TestCase
{

    use DatabaseTransactions;

    /** @test */
    public function upload()
    {
        \Storage::fake('app');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');
        $this->actingAs($user, 'api')
            ->json('PUT', route('file.store'), compact('file'))
            ->dump()
            ->assertJson([
                'file' => [
                    'original_filename' => $file->getClientOriginalName(),
                ],
                'view_url' => route('file.show', ['file' => $file->getClientOriginalName()])
            ]);
    }

    /** @test */
    public function unauthenticatedFileView()
    {
        $file = File::factory()->create();
    }
}
