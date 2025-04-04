<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileCannotBeDeletedException;
use App\Jobs\PurgeCDNCacheJob;
use App\Models\File;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{

    private Filesystem $disk;

    public function __construct(
        public CDNService $cdnService
    )
    {
        $this->disk = Storage::disk(config('upload.storage.disk'));
    }

    public function getFilename(UploadedFile $file): string
    {
        return implode('_', [
                Str::orderedUuid(),
                Str::random(12),
            ]) . "." . $file->getClientOriginalExtension();
    }

    public function storeUploadedFile(UploadedFile $file, User $user): File
    {
        $storagePath = config('upload.storage.path');
        $name = $this->getFilename($file);
        $path = $this->disk->putFileAs($storagePath, $file, $name, [
            'visibility' => 'public'
        ]);
        $file = new File([
            'file_location' => $path,
            'filename' => $name,
            'original_filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
        $user->files()->save($file);
        return $file;
    }

    /**
     * Deletes a file from the disk, database, and purges it from the CDN
     * @throws FileCannotBeDeletedException
     */
    public function delete(File $file): bool
    {
        $result = $this->disk->delete($file->file_location);
        if (!$result) {
            throw new FileCannotBeDeletedException("Failed to delete the file from disk");
        }
        //this bit of code is called
        $slug = $file->file_location;
        $file->delete();
        dispatch(new PurgeCDNCacheJob($slug));
        return $result;
    }
}
