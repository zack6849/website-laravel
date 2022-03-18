<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan upload storage for files uploaded via sftp to add them to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $real_path = Storage::path(config('upload.storage.path'));
        $this->info("Scanning $real_path for new uploads...");
        $files = Storage::files(config("upload.storage.path"));
        $existing_files = File::all()->pluck('file_location')->all();
        $new_files = array_diff($files, $existing_files);
        $this->info("Found ". count($new_files) . " new files to register!");
        foreach ($new_files as $file_name){
            $file_path =  $file_name;
            $file_basename = basename($file_name);
            $file = new File();
            $file->user_id = 1; //nobody can upload files via sftp aside from me, anyways.
            $file->file_location = $file_path;
            $file->original_filename = $file_basename;
            $file->mime = Storage::mimeType($file_path);
            $file->created_at = Storage::lastModified($file_name);
            $file->updated_at = Storage::lastModified($file_name);
            $file->size = Storage::size($file_name);
            $file->filename = $file_basename;
            $file->save();
            $this->info("- Added $file_path to the database");
        }
        return 1;
    }
}
