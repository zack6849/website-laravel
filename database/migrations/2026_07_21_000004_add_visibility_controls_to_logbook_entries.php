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
            $table->string('qrz_logid')->nullable()->after('id')->index();
            $table->string('entry_key')->nullable()->after('qrz_logid')->index();
            $table->boolean('hidden_from_public')->default(false)->after('category')->index();
        });

        Schema::create('logbook_entry_visibility_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('qrz_logid')->nullable()->unique();
            $table->string('entry_key')->unique();
            $table->boolean('hidden_from_public')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_entry_visibility_overrides');

        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->dropColumn('qrz_logid');
            $table->dropColumn('entry_key');
            $table->dropColumn('hidden_from_public');
        });
    }
};
