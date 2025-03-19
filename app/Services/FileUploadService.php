<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\File;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class FileUploadService
{

    private Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(config('upload.storage.disk'));
    }

    private function getFilename(UploadedFile $file): string
    {
        return implode('_', [
                Str::orderedUuid(),
                Str::random(12),
            ]) . "." . $file->getClientOriginalExtension();
    }

    public function storeUploadedFile(UploadedFile $file): File
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
        auth()->user()->files()->save($file);
        return $file;
    }

    /**
     * Deletes a file from the disk, database, and purges it from the CDN
     * @throws Throwable if the file cannot be deleted
     */
    public function delete(File $file): bool
    {
        $result = $this->disk->delete($file->file_location);
        if (!$result) {
            throw new Exception("Failed to delete file $file->file_location");
        }
        $slug = $file->file_location;
        $file->delete();
        //try several times to remove the file from the CDN
        dispatch(function () use ($slug) {
            retry(5, function () use ($slug) {
                $service = resolve(CDNService::class);
                $success = $service->purgeCache(config('services.digitalocean.cdn_endpoint_id'), $slug);
                if (!$success) {
                    throw new Exception("Failed to purge CDN cache for file $slug");
                }
            }, 1000);
        })->afterResponse();
        return $result;
    }
}
