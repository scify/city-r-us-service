<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('mission_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('missions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('img_name')->nullable();
            $table->timestamps();

            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('mission_types');
        });
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('missions');
        Schema::dropIfExists('mission_types');

    }

}
