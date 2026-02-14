<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // default limit: 30 per day
            $table->integer("lookup_limit")->default(30);
            $table->integer("lookup_decay_rate")->default(86400);
            $table->boolean("horizon_access")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("lookup_limit");
            $table->dropColumn("horizon_access");
        });
    }
};
