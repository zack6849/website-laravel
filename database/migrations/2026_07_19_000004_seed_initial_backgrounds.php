<?php

declare(strict_types=1);

use Database\Seeders\BackgroundSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        (new BackgroundSeeder())->run();
    }

    public function down(): void
    {
        $keys = array_column((new BackgroundSeeder())->backgrounds(), 'key');

        DB::table('backgrounds')->whereIn('key', $keys)->delete();
    }
};
