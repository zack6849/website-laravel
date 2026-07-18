<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('upload.storage.disk'));
        $this->user = User::factory()->create();
    }

    #[Test]
    public function upload()
    {
        $file = UploadedFile::fake()->image('photo.jpg');
        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', route('file.store'), compact('file'));
        $latestFile = File::latest()->first();
        $response->assertJson([
            'data' => [
                'user' => [
                    'email' => $this->user->email,
                ],
                'file' => [
                    'original_filename' => $file->getClientOriginalName(),
                    'filename' => $latestFile->filename,
                ],
            ]
        ]);
        $expectedUrl = route('file.show', ['file' => $latestFile->filename]);
        $response->assertJsonFragment(['view_url' => $expectedUrl]);
        //assert we have a temporarily signed URl in the response
        $response->assertSeeText("?signature=");
    }

    #[Test]
    public function malformedUploadReturnsValidationError()
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', route('file.store'), ['file' => 'not-an-upload']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }
}
