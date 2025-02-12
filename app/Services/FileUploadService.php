<?php

namespace App\Services;

use App\Models\File;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{

    private Filesystem $disk;

    public function __construct(
        private CDNService $cdnService
    )
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
            'user_id' => auth()->user()->id,
        ]);
        $file->save();
        return $file;
    }

    public function delete(File $file): bool
    {
        $result = $this->disk->delete($file->file_location);
        if ($result) {
            $this->cdnService->purgeCache(
                config('services.digitalocean.cdn.id'),
                $file->file_location
            );
            $file->delete();
        }else {
            throw new Exception("Failed to delete file $file->file_location");
        }
        return $result;
    }
}
