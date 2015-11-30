<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateObservationsAndMeasurementsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('observations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device_uuid');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('observation_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('measurements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('value');
            $table->string('unit');
            $table->string('latitude');
            $table->string('longitude');
            $table->dateTime('observation_date')->nullable();

            $table->integer('observation_id')->unsigned();
            $table->foreign('observation_id')->references('id')->on('observations');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('measurements');
        Schema::dropIfExists('observations');
    }

}
