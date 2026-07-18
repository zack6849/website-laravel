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
        Storage::fake(config('upload.storage.disk'));
        Bus::fake();
        $owner = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id, 'filename' => $this->safeFilename()]);

        $response = $this->actingAs($owner)->post(route('file.destroy', ['file' => $file->filename]));

        $response->assertRedirect(route('file.index'));
        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }

    #[Test]
    public function nonOwnerCannotDeleteAnotherUsersFileViaDestroy(): void
    {
        Storage::fake(config('upload.storage.disk'));
        Bus::fake();
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id, 'filename' => $this->safeFilename()]);

        $response = $this->actingAs($otherUser)->post(route('file.destroy', ['file' => $file->filename]));

        $response->assertForbidden();
        $this->assertDatabaseHas('files', ['id' => $file->id]);
    }

    #[Test]
    public function nonOwnerCannotReachTheDeleteConfirmationPageEvenWithAValidSignature(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id, 'filename' => $this->safeFilename()]);

        //a validly-signed delete URL, but requested by someone who isn't the file's owner
        $response = $this->actingAs($otherUser)->get($file->delete_url);

        $response->assertForbidden();
    }

    #[Test]
    public function ownerCanReachTheDeleteConfirmationPage(): void
    {
        $owner = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id, 'filename' => $this->safeFilename()]);

        $response = $this->actingAs($owner)->get($file->delete_url);

        $response->assertOk();
    }
}
