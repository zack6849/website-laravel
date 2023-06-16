<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->foreignId('park_id')->nullable()->references('id')->on('pota_parks');
            $table->string("category")->default("default");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            $table->dropColumn("park_id");
            $table->dropColumn("category");
        });
    }
};
