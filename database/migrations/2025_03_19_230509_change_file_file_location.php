<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    private const OLD_PREFIX = 'user_uploads/';
    private const NEW_PREFIX = 'site_uploads/';


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE files SET file_location = REPLACE(file_location, :old_prefix, :new_prefix) WHERE file_location LIKE :old_prefix_like", [
            'old_prefix' => self::OLD_PREFIX, //search for
            'new_prefix' => self::NEW_PREFIX, //replace with
            'old_prefix_like' => self::OLD_PREFIX . "%", //where starts with
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE files SET file_location = REPLACE(file_location, :new_prefix, :old_prefix) WHERE file_location LIKE :new_prefix_like", [
            'new_prefix' => self::NEW_PREFIX, //search for
            'old_prefix' => self::OLD_PREFIX, //replace with
            'new_prefix_like' => self::NEW_PREFIX . "%", //where starts with
        ]);
    }
};
