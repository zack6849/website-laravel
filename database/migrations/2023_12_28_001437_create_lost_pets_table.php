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
        Schema::create('lost_pets', function (Blueprint $table) {
            $table->id();
            $table->string('animal_id');
            $table->string('name')->nullable();
            $table->string('breed')->nullable();
            $table->string('color')->nullable();
            $table->string('sex')->nullable();
            $table->string('photo')->nullable();
            $table->integer('age')->nullable();
            $table->string('age_group')->nullable();
            $table->string('status')->nullable();
            $table->string('rescue_name')->nullable();
            $table->string('rescue_address')->nullable();
            $table->string('rescue_email')->nullable();
            $table->string('rescue_phone')->nullable();
            $table->dateTime('intake_date')->nullable();
            $table->string('intake_type')->nullable();
            $table->longText("poster_text")->nullable();
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
        Schema::dropIfExists('lost_pets');
    }
};
