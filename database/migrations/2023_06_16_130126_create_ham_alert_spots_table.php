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
        Schema::create('ham_alert_spots', function (Blueprint $table) {
            $table->id();
            $table->string("callsign");
            $table->string("spotter_callsign");
            $table->string("frequency");
            $table->string("band");
            $table->string("spotter_entity");
            $table->string("mode");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ham_alert_spots');
    }
};
