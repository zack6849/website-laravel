<?php

namespace App\Services;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{

    private FilesystemAdapter $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(config('app.upload_storage'));
    }

    public function putFile($file, $path)
    {
        $this->disk->putFile($path, $file);
    }

    public function delete($path)
    {
        $this->disk->delete($path);
    }
}
