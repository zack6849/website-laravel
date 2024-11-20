<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ConvertUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $disk = Storage::disk('spaces');
        $files = $disk->listContents('site_uploads');
        $progressbar = $this->output->createProgressBar(count($files->toArray()));
        $progressbar->display();
        foreach ($disk->listContents('site_uploads') as $file){
            if($file->visibility() !== 'public'){
                $this->info("File {$file->path()} is not public, converting...");
                $disk->setVisibility($file->path(), 'public');
            }
            $progressbar->advance();
        }
        $progressbar->finish();
    }
}
