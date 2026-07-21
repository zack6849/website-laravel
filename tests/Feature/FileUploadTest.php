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

        $deleteUrl = $response->json('data.delete_url');
        parse_str(parse_url($deleteUrl, PHP_URL_QUERY), $query);

        $this->assertArrayHasKey('signature', $query);
        $this->assertArrayNotHasKey('expires', $query);
    }

    #[Test]
    public function malformedUploadReturnsValidationError()
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', route('file.store'), ['file' => 'not-an-upload']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    #[Test]
    public function riskyExtensionsAreRejected(): void
    {
        $file = UploadedFile::fake()->create('payload.php', 1, 'application/x-php');

        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', route('file.store'), compact('file'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    #[Test]
    public function longOriginalFilenamesAreRejectedBeforeStorage(): void
    {
        $file = UploadedFile::fake()->image(str_repeat('a', 252) . '.jpg');

        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', route('file.store'), compact('file'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
        $this->assertSame([], Storage::disk(config('upload.storage.disk'))->allFiles(config('upload.storage.path')));
    }

    #[Test]
    public function statusFlashMessagesAreEscaped(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['status' => '<script>alert("xss")</script>'])
            ->get(route('file.index'));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert("xss")</script>', false);
    }
}
