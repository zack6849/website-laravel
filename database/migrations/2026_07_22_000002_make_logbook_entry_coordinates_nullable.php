<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->string('from_coordinates')->nullable()->change();
            $table->string('from_latitude')->nullable()->change();
            $table->string('from_longitude')->nullable()->change();
            $table->string('to_latitude')->nullable()->change();
            $table->string('to_longitude')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->string('from_coordinates')->nullable(false)->change();
            $table->string('from_latitude')->nullable(false)->change();
            $table->string('from_longitude')->nullable(false)->change();
            $table->string('to_latitude')->nullable(false)->change();
            $table->string('to_longitude')->nullable(false)->change();
        });
    }
};
