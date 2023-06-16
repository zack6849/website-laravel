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
        Schema::create('pota_parks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference')->unique();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string("grid4")->nullable();
            $table->string("grid6")->nullable();
            $table->foreignId("park_type_id")->references("id")->on("pota_park_types");
            $table->boolean("active")->default(false);
            $table->string("comments")->nullable();
            $table->string("location")->nullable();
            $table->longText("raw_data")->comment('Raw data from the POTA API');
            $table->date('first_activation_at')->nullable();
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
        Schema::dropIfExists('pota_parks');
    }
};
