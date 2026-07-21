<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table): void {
            $table->string('to_city')->nullable()->after('to_callsign');
            $table->string('to_state')->nullable()->after('to_city');
            $table->string('to_county')->nullable()->after('to_state');
        });
    }

    public function down(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table): void {
            $table->dropColumn(['to_city', 'to_state', 'to_county']);
        });
    }
};
