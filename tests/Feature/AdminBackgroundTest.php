<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Backgrounds\DeleteBackground;
use App\Actions\Backgrounds\PinBackground;
use App\Actions\Backgrounds\SaveBackground;
use App\Livewire\Admin\BackgroundForm;
use App\Livewire\Admin\BackgroundIndex;
use App\Models\Background;
use App\Models\User;
use App\Services\Backgrounds\BackgroundImageStorage;
use App\Services\Backgrounds\BackgroundSelectionCache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class AdminBackgroundTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function member(): User
    {
        return User::factory()->create(['is_admin' => false]);
    }

    #[Test]
    public function guestsAreRedirectedFromAdmin(): void
    {
        $this->get(route('admin.backgrounds.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function nonAdminsAreForbidden(): void
    {
        $this->actingAs($this->member())
            ->get(route('admin.backgrounds.index'))
            ->assertForbidden();
    }

    #[Test]
    public function adminsCanViewTheBackgroundIndex(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.backgrounds.index'))
            ->assertOk()
            ->assertSee('New background');
    }

    #[Test]
    public function backgroundStatusFlashIsOnlyShownOnceOnTheIndex(): void
    {
        $response = $this->actingAs($this->admin())
            ->withSession(['status' => 'Background saved.'])
            ->get(route('admin.backgrounds.index'));

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'Background saved.'));
    }

    #[Test]
    public function adminCanCreateABackgroundWithResponsiveCrops(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Sunset')
            ->set('form.image', 'img/bg/pier_night.jpg')
            ->set('form.position.x', '40%')
            ->set('form.position.y', '60%')
            ->set('form.size', 'cover')
            ->set('form.lg.x', '70%')
            ->set('form.lg.size', '100% auto')
            ->call('save')
            ->assertRedirect(route('admin.backgrounds.index'));

        $background = Background::where('title', 'Sunset')->firstOrFail();

        $this->assertSame('img/bg/pier_night.jpg', $background->image);
        $this->assertSame('40%', $background->position['x']);
        $this->assertSame('70%', $background->variants['lg']['position']['x']);
        $this->assertSame('100% auto', $background->variants['lg']['size']);
        $this->assertNotEmpty($background->key);
    }

    #[Test]
    public function backgroundFormRendersTheHomepageBannerPreview(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Preview')
            ->set('form.image', 'img/bg/pier_night.jpg')
            ->assertSee('top-banner-surface', false)
            ->assertSee('--top-banner-bg-url', false)
            ->assertSee('Places you can find me');
    }

    #[Test]
    public function previewTabsRenderTheirMatchingCssVariables(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.image', 'img/bg/pier_night.jpg')
            ->set('form.position.x', '10%')
            ->set('form.position.y', '20%')
            ->set('form.size', '80% auto')
            ->set('form.sm.x', '30%')
            ->set('form.sm.y', '40%')
            ->set('form.sm.size', '90% auto')
            ->set('previewBreakpoint', 'base')
            ->assertSee('top-banner-preview-base', false)
            ->assertSee('--top-banner-bg-x: 10%', false)
            ->assertSee('--top-banner-bg-y: 20%', false)
            ->assertSee('--top-banner-bg-size: 80% auto', false)
            ->set('previewBreakpoint', 'sm')
            ->assertSee('top-banner-preview-sm', false)
            ->assertSee('--top-banner-bg-x-sm: 30%', false)
            ->assertSee('--top-banner-bg-y-sm: 40%', false)
            ->assertSee('--top-banner-bg-size-sm: 90% auto', false)
            ->set('previewBreakpoint', 'lg')
            ->set('form.lg.x', '12.5%')
            ->set('form.lg.y', '143%')
            ->set('form.lg.size', '125% auto')
            ->assertSee('top-banner-preview-shell-lg', false)
            ->assertSee('top-banner-preview-lg', false)
            ->assertSee('--top-banner-bg-x-lg: 12.5%', false)
            ->assertSee('--top-banner-bg-y-lg: 143%', false)
            ->assertSee('--top-banner-bg-size-lg: 125% auto', false);
    }

    #[Test]
    public function onlyTheSelectedCropControlsAreRendered(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->assertSeeHtmlInOrder(['Live preview', 'Mobile crop', 'Title'])
            ->assertSee('Mobile crop')
            ->assertDontSee('Tablet crop')
            ->assertDontSee('Desktop crop')
            ->set('previewBreakpoint', 'sm')
            ->assertDontSee('Mobile crop')
            ->assertSee('Tablet crop')
            ->assertDontSee('Desktop crop')
            ->set('previewBreakpoint', 'lg')
            ->assertDontSee('Mobile crop')
            ->assertDontSee('Tablet crop')
            ->assertSee('Desktop crop');
    }

    #[Test]
    public function focusSlidersUpdateCropPercentFields(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('focus.base.x', 44.5)
            ->assertSet('form.position.x', '44.5%')
            ->set('focus.base.y', 106.3)
            ->assertSet('form.position.y', '106.3%')
            ->set('focus.base.size', 125.5)
            ->assertSet('form.size', '125.5% auto')
            ->set('focus.sm.size', 75)
            ->assertSet('form.sm.size', '75% auto')
            ->set('focus.lg.y', 143)
            ->assertSet('form.lg.y', '143%');
    }

    #[Test]
    public function creatingWithoutAnImageFails(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'No image')
            ->set('form.image', '')
            ->call('save')
            ->assertHasErrors('form.image');

        $this->assertDatabaseMissing('backgrounds', ['title' => 'No image']);
    }

    #[Test]
    public function pinningOneBackgroundUnpinsOthers(): void
    {
        $first = Background::create([
            'key' => 'first',
            'title' => 'First',
            'image' => 'img/bg/pier_night.jpg',
            'pinned' => true,
        ]);
        $second = Background::create([
            'key' => 'second',
            'title' => 'Second',
            'image' => 'img/bg/bg_clownfish.jpg',
        ]);

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('togglePinned', $second->id);

        $this->assertFalse($first->fresh()->pinned);
        $this->assertTrue($second->fresh()->pinned);
    }

    #[Test]
    public function adminCanScheduleAThemedDay(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Christmas')
            ->set('form.image', 'img/bg/pier_night.jpg')
            ->set('form.schedule', [['type' => 'date', 'month' => 12, 'day' => 25]])
            ->call('save')
            ->assertRedirect(route('admin.backgrounds.index'));

        $background = Background::where('title', 'Christmas')->firstOrFail();

        $this->assertSame(12, $background->schedule[0]['month']);
        $this->assertSame(25, $background->schedule[0]['day']);
    }

    #[Test]
    public function invalidScheduleRulesFailValidation(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Impossible Date')
            ->set('form.image', 'img/bg/pier_night.jpg')
            ->set('form.schedule', [['type' => 'date', 'month' => 2, 'day' => 31]])
            ->call('save')
            ->assertHasErrors('form.schedule');

        $this->assertDatabaseMissing('backgrounds', ['title' => 'Impossible Date']);
    }

    #[Test]
    public function invalidBackgroundCssValuesFailValidation(): void
    {
        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Injected Styles')
            ->set('form.image', 'img/bg/pier_night.jpg')
            ->set('form.position.x', '50%;color:red')
            ->set('form.size', 'cover;background:red')
            ->call('save')
            ->assertHasErrors(['form.position.x', 'form.size']);

        $this->assertDatabaseMissing('backgrounds', ['title' => 'Injected Styles']);
    }

    #[Test]
    public function uploadedBackgroundFileIsDeletedWhenBackgroundIsDeleted(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Uploaded')
            ->set('form.upload', UploadedFile::fake()->image('uploaded.jpg'))
            ->call('save')
            ->assertRedirect(route('admin.backgrounds.index'));

        $background = Background::where('title', 'Uploaded')->firstOrFail();
        $diskPath = Str::after($background->image, 'storage/');

        Storage::disk('public')->assertExists($diskPath);

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('delete', $background->id);

        $this->assertDatabaseMissing('backgrounds', ['id' => $background->id]);
        Storage::disk('public')->assertMissing($diskPath);
    }

    #[Test]
    public function replacingUploadedBackgroundFileDeletesTheOldManagedFile(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Replaceable')
            ->set('form.upload', UploadedFile::fake()->image('first.jpg'))
            ->call('save')
            ->assertRedirect(route('admin.backgrounds.index'));

        $background = Background::where('title', 'Replaceable')->firstOrFail();
        $oldDiskPath = Str::after($background->image, 'storage/');

        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class, ['backgroundId' => $background->id])
            ->set('form.title', 'Replaceable')
            ->set('form.upload', UploadedFile::fake()->image('second.jpg'))
            ->call('save')
            ->assertRedirect(route('admin.backgrounds.index'));

        $newDiskPath = Str::after($background->fresh()->image, 'storage/');

        Storage::disk('public')->assertMissing($oldDiskPath);
        Storage::disk('public')->assertExists($newDiskPath);
    }

    #[Test]
    public function deletingOneBackgroundDoesNotDeleteAManagedFileStillReferencedByAnotherBackground(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('backgrounds/shared.jpg', 'image-bytes');

        $first = Background::create([
            'key' => 'first-shared',
            'title' => 'First Shared',
            'image' => 'storage/backgrounds/shared.jpg',
        ]);
        $second = Background::create([
            'key' => 'second-shared',
            'title' => 'Second Shared',
            'image' => 'storage/backgrounds/shared.jpg',
        ]);

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('delete', $first->id);

        Storage::disk('public')->assertExists('backgrounds/shared.jpg');

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('delete', $second->id);

        Storage::disk('public')->assertMissing('backgrounds/shared.jpg');
    }

    #[Test]
    public function theOnlyEnabledBackgroundCannotBeDeleted(): void
    {
        Background::query()->delete();

        $only = Background::create([
            'key' => 'only',
            'title' => 'Only',
            'image' => 'img/bg/pier_night.jpg',
        ]);

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('delete', $only->id)
            ->assertHasErrors('backgrounds');

        $this->assertDatabaseHas('backgrounds', ['id' => $only->id]);
    }

    #[Test]
    public function theOnlyEnabledBackgroundCannotBeDisabled(): void
    {
        Background::query()->delete();

        $only = Background::create([
            'key' => 'only',
            'title' => 'Only',
            'image' => 'img/bg/pier_night.jpg',
        ]);

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('toggleEnabled', $only->id)
            ->assertHasErrors('backgrounds');

        $this->assertTrue($only->fresh()->enabled);
    }

    #[Test]
    public function theOnlyEnabledBackgroundCannotBeDisabledFromTheEditForm(): void
    {
        Background::query()->delete();

        $only = Background::create([
            'key' => 'only',
            'title' => 'Only',
            'image' => 'img/bg/pier_night.jpg',
        ]);

        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class, ['backgroundId' => $only->id])
            ->set('form.enabled', false)
            ->call('save')
            ->assertHasErrors('form.enabled');

        $this->assertTrue($only->fresh()->enabled);
    }

    #[Test]
    public function aBackgroundCanBeDeletedWhenAnotherEnabledBackgroundRemains(): void
    {
        $keep = Background::create([
            'key' => 'keep',
            'title' => 'Keep',
            'image' => 'img/bg/pier_night.jpg',
        ]);
        $remove = Background::create([
            'key' => 'remove',
            'title' => 'Remove',
            'image' => 'img/bg/bg_clownfish.jpg',
        ]);

        Livewire::actingAs($this->admin())
            ->test(BackgroundIndex::class)
            ->call('delete', $remove->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('backgrounds', ['id' => $remove->id]);
        $this->assertDatabaseHas('backgrounds', ['id' => $keep->id]);
    }

    #[Test]
    public function phpFilesCannotBeUploadedAsBackgrounds(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin())
            ->test(BackgroundForm::class)
            ->set('form.title', 'Shell')
            ->set('form.upload', UploadedFile::fake()->create('shell.php', 1, 'application/x-php'))
            ->call('save')
            ->assertHasErrors();

        $this->assertDatabaseMissing('backgrounds', ['title' => 'Shell']);
        $this->assertSame([], Storage::disk('public')->allFiles('backgrounds'));
    }

    #[Test]
    public function backgroundUploadRulesRejectPhpExtensions(): void
    {
        $validator = Validator::make(
            ['upload' => UploadedFile::fake()->create('shell.php', 1, 'application/x-php')],
            ['upload' => BackgroundImageStorage::uploadRules()],
        );

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('upload', $validator->errors()->messages());
    }

    #[Test]
    public function deleteStillCompletesWhenImageCleanupFailsAfterCommit(): void
    {
        Background::create([
            'key' => 'keep',
            'title' => 'Keep',
            'image' => 'img/bg/pier_night.jpg',
        ]);
        $remove = Background::create([
            'key' => 'remove',
            'title' => 'Remove',
            'image' => 'storage/backgrounds/remove.jpg',
        ]);

        $images = Mockery::mock(BackgroundImageStorage::class);
        $images->shouldReceive('managedPublicPathsFor')
            ->once()
            ->with(Mockery::type(Background::class))
            ->andReturn(['storage/backgrounds/remove.jpg']);
        $images->shouldReceive('deleteUnreferenced')
            ->once()
            ->with(['storage/backgrounds/remove.jpg'])
            ->andThrow(new RuntimeException('Disk delete failed.'));

        $cache = Mockery::mock(BackgroundSelectionCache::class);
        $cache->shouldReceive('forget')->once();

        (new DeleteBackground($images, $cache))->delete($remove);

        $this->assertDatabaseMissing('backgrounds', ['id' => $remove->id]);
    }

    #[Test]
    public function saveStillCompletesWhenOldImageCleanupFailsAfterCommit(): void
    {
        $background = Background::create([
            'key' => 'editable',
            'title' => 'Editable',
            'image' => 'storage/backgrounds/old.jpg',
        ]);

        $images = Mockery::mock(BackgroundImageStorage::class);
        $images->shouldReceive('managedPublicPathsFor')
            ->twice()
            ->with(Mockery::type(Background::class))
            ->andReturn(['storage/backgrounds/old.jpg'], ['img/bg/pier_night.jpg']);
        $images->shouldReceive('deleteUnreferenced')
            ->once()
            ->with(['storage/backgrounds/old.jpg'])
            ->andThrow(new RuntimeException('Disk delete failed.'));

        $saved = (new SaveBackground(
            $images,
            resolve(BackgroundSelectionCache::class),
            resolve(PinBackground::class),
        ))->save($this->backgroundActionData([
            'background_id' => $background->id,
            'title' => 'Edited',
            'image' => 'img/bg/pier_night.jpg',
        ]));

        $this->assertSame('Edited', $saved->title);
        $this->assertDatabaseHas('backgrounds', [
            'id' => $background->id,
            'title' => 'Edited',
            'image' => 'img/bg/pier_night.jpg',
        ]);
    }

    #[Test]
    public function failedSaveDeletesTheNewlyStoredUpload(): void
    {
        Storage::fake('public');

        try {
            resolve(SaveBackground::class)->save(
                [
                    'background_id' => 999,
                    'title' => 'Will Fail',
                    'description' => null,
                    'image' => '',
                    'overlay' => 0.68,
                    'size' => 'cover',
                    'position' => ['x' => '50%', 'y' => '50%'],
                    'variants' => null,
                    'schedule' => null,
                    'enabled' => true,
                    'weight' => 1,
                    'pinned' => false,
                ],
                UploadedFile::fake()->image('orphan.jpg'),
            );

            $this->fail('Expected the save action to fail for a missing background.');
        } catch (ModelNotFoundException) {
            $this->assertSame([], Storage::disk('public')->allFiles('backgrounds'));
        }
    }

    #[Test]
    public function failedSaveRethrowsOriginalExceptionWhenUploadCleanupFails(): void
    {
        $images = Mockery::mock(BackgroundImageStorage::class);
        $images->shouldReceive('store')
            ->once()
            ->with(Mockery::type(UploadedFile::class))
            ->andReturn('storage/backgrounds/orphan.jpg');
        $images->shouldReceive('deleteUnreferenced')
            ->once()
            ->with(['storage/backgrounds/orphan.jpg'])
            ->andThrow(new RuntimeException('Disk delete failed.'));

        $this->expectException(ModelNotFoundException::class);

        (new SaveBackground(
            $images,
            resolve(BackgroundSelectionCache::class),
            resolve(PinBackground::class),
        ))->save(
            $this->backgroundActionData([
                'background_id' => 999,
                'title' => 'Will Fail',
                'image' => '',
            ]),
            UploadedFile::fake()->image('orphan.jpg'),
        );
    }

    private function backgroundActionData(array $overrides = []): array
    {
        return array_merge([
            'background_id' => null,
            'title' => 'Background',
            'description' => null,
            'image' => 'img/bg/pier_night.jpg',
            'overlay' => 0.68,
            'size' => 'cover',
            'position' => ['x' => '50%', 'y' => '50%'],
            'variants' => null,
            'schedule' => null,
            'enabled' => true,
            'weight' => 1,
            'pinned' => false,
        ], $overrides);
    }
}
