<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileAuthorizationTest extends TestCase
{
    private User $owner;
    private File $file;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('upload.storage.disk'));
        $this->owner = User::factory()->create();
        $this->file = File::factory()->create(['user_id' => $this->owner->id, 'filename' => $this->safeFilename()]);
    }

    //FileFactory's default filename() uses asciify() and can produce URL-unsafe
    //characters that break route-model-binding on a generated URL; real uploads use
    //safe UUID-based names (FileUploadService::getFilename()), so use one here too
    private function safeFilename(): string
    {
        return Str::uuid() . '.jpg';
    }

    #[Test]
    public function ownerCanDeleteTheirOwnFile(): void
    {
        Bus::fake();

        $response = $this->actingAs($this->owner)->post(route('file.destroy', ['file' => $this->file->filename]));

        $response->assertRedirect(route('file.index'));
        $this->assertDatabaseMissing('files', ['id' => $this->file->id]);
    }

    #[Test]
    public function nonOwnerCannotDeleteAnotherUsersFileViaDestroy(): void
    {
        Bus::fake();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->post(route('file.destroy', ['file' => $this->file->filename]));

        $response->assertForbidden();
        $this->assertDatabaseHas('files', ['id' => $this->file->id]);
    }

    #[Test]
    public function nonOwnerCannotReachTheDeleteConfirmationPageEvenWithAValidSignature(): void
    {
        $otherUser = User::factory()->create();

        //a validly-signed delete URL, but requested by someone who isn't the file's owner
        $response = $this->actingAs($otherUser)->get($this->file->delete_url);

        $response->assertForbidden();
    }

    #[Test]
    public function ownerCanReachTheDeleteConfirmationPage(): void
    {
        $response = $this->actingAs($this->owner)->get($this->file->delete_url);

        $response->assertOk();
    }
}
