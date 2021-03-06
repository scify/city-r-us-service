<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_observation_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('points')->nullable();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('mission_id')->unsigned();
            $table->foreign('mission_id')->references('id')->on('missions');

            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('user_invite_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('points')->nullable();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('invite_id')->unsigned();
            $table->foreign('invite_id')->references('id')->on('invites');

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
        Schema::dropIfExists('user_observation_points');
        Schema::dropIfExists('user_invite_points');
    }

}
