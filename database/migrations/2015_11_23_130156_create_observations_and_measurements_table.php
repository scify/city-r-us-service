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
            $table->increments('observation_id');
            $table->string('device_uuid');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('observation_date')->nullable();
        });

        Schema::create('measurements', function (Blueprint $table) {
            $table->increments('measurement_id');
            $table->string('type');
            $table->string('value');
            $table->string('unit');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('observation_date')->nullable();

            $table->integer('observation_id')->unsigned();
            $table->foreign('observation_id')->references('observation_id')->on('observations');
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
