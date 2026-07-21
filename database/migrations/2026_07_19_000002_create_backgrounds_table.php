<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backgrounds', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image');
            $table->float('overlay')->default(0.68);
            $table->string('size')->default('cover');
            // Base focal point, e.g. {"x":"50%","y":"50%"}
            $table->json('position')->nullable();
            // Per-breakpoint overrides: {"base":{...},"sm":{...},"lg":{...}}
            $table->json('variants')->nullable();
            // Themed-day rules; a background is eligible when any rule matches today.
            $table->json('schedule')->nullable();
            $table->boolean('enabled')->default(true);
            // Relative likelihood of being picked by the weighted random selection.
            $table->unsignedInteger('weight')->default(1);
            // When true this background overrides the random/scheduled selection.
            $table->boolean('pinned')->default(false);
            $table->timestamps();

            $table->index('enabled');
            $table->index('pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backgrounds');
    }
};
