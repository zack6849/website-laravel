<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Files\FileIndex;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;
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

    #[Test]
    public function legacyUploadRedirectRouteRedirectsToTheFileUrl(): void
    {
        $response = $this->get(route('file.old.redirect', ['file' => $this->file->filename]));

        $response->assertStatus(301);
        $response->assertRedirect($this->file->url);
    }

    #[Test]
    public function fileSearchDoesNotWidenTheOwnerScope(): void
    {
        $ownerMatch = File::factory()->create([
            'user_id' => $this->owner->id,
            'filename' => $this->safeFilename(),
            'original_filename' => 'shared-search-owner.jpg',
        ]);
        $otherUser = User::factory()->create();
        $otherMatch = File::factory()->create([
            'user_id' => $otherUser->id,
            'filename' => $this->safeFilename(),
            'original_filename' => 'shared-search-other.jpg',
            'mime' => 'shared-search-token',
        ]);

        $results = File::forUser($this->owner)
            ->search('shared-search')
            ->pluck('id');

        $this->assertTrue($results->contains($ownerMatch->id));
        $this->assertFalse($results->contains($otherMatch->id));
    }

    #[Test]
    public function fileIndexOnlyListsTheAuthenticatedUsersFiles(): void
    {
        $otherUser = User::factory()->create();
        $otherFile = File::factory()->create([
            'user_id' => $otherUser->id,
            'filename' => $this->safeFilename(),
            'original_filename' => 'someone-elses-file.jpg',
        ]);

        Livewire::actingAs($this->owner)
            ->test(FileIndex::class)
            ->assertSee($this->file->original_filename)
            ->assertDontSee($otherFile->original_filename);
    }

    #[Test]
    public function fileIndexDateFiltersScopeToTheAuthenticatedUsersFiles(): void
    {
        File::whereKey($this->file)->update([
            'created_at' => Carbon::create(2026, 3, 10),
            'updated_at' => Carbon::create(2026, 3, 10),
        ]);
        $laterFile = File::factory()->create([
            'user_id' => $this->owner->id,
            'filename' => $this->safeFilename(),
            'original_filename' => 'later-upload.jpg',
            'created_at' => Carbon::create(2026, 4, 12),
            'updated_at' => Carbon::create(2026, 4, 12),
        ]);

        $otherUser = User::factory()->create();
        $otherFile = File::factory()->create([
            'user_id' => $otherUser->id,
            'filename' => $this->safeFilename(),
            'original_filename' => 'other-users-old-file.jpg',
            'created_at' => Carbon::create(2020, 1, 1),
            'updated_at' => Carbon::create(2020, 1, 1),
        ]);

        Livewire::actingAs($this->owner)
            ->test(FileIndex::class)
            ->set('createdFrom', '2026-04-01')
            ->assertSee($laterFile->original_filename)
            ->assertDontSee($this->file->original_filename)
            ->set('createdFrom', '')
            ->set('createdTo', '2026-03-31')
            ->assertSee($this->file->original_filename)
            ->assertDontSee($laterFile->original_filename)
            ->assertDontSee($otherFile->original_filename);
    }

    #[Test]
    public function ownerCanDeleteTheirOwnFileFromTheIndex(): void
    {
        Bus::fake();

        Livewire::actingAs($this->owner)
            ->test(FileIndex::class)
            ->call('delete', $this->file->id)
            ->assertSet('status', 'Your file was deleted!');

        $this->assertDatabaseMissing('files', ['id' => $this->file->id]);
    }

    #[Test]
    public function nonOwnerCannotDeleteAnotherUsersFileFromTheIndex(): void
    {
        Bus::fake();
        $otherUser = User::factory()->create();

        try {
            Livewire::actingAs($otherUser)
                ->test(FileIndex::class)
                ->call('delete', $this->file->id);

            $this->fail('Expected the delete to fail for a non-owner.');
        } catch (ModelNotFoundException) {
            // the owner scope hides other users' files entirely
        }

        $this->assertDatabaseHas('files', ['id' => $this->file->id]);
    }
}
