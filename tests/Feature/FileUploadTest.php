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
        \Storage::fake(config('upload.storage.disk'));
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');
        $response = $this->actingAs($user, 'api')
            ->json('PUT', route('file.store'), compact('file'));
        $latestFile = File::latest()->first();
        $response->assertJson([
            'data' => [
                'user' => [
                    'email' => $user->email,
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
}
