<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId("from_callsign")->references("id")->on("callsigns");
            $table->foreignId("to_callsign")->references("id")->on("callsigns");
            $table->double("frequency");
            $table->string("band");
            $table->string("mode");
            $table->string("rst_sent")->nullable();
            $table->string("rst_received")->nullable();
            $table->string("from_grid");
            $table->string("from_coordinates");
            $table->string("from_latitude");
            $table->string("from_longitude");
            $table->string("to_grid")->nullable();
            $table->string("to_coordinates")->nullable();
            $table->string("to_latitude");
            $table->string("to_longitude");
            $table->integer("distance")->comment("distance in miles")->nullable();
            $table->string("comments")->nullable();
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
        Schema::dropIfExists('logbook_entries');
    }
};
