<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        Schema::create('devices', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('device_uuid');
            $table->string('model');
            $table->string('manufacturer');
            $table->string('type')->default('smartphone');
            $table->string('status')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users_devices', function(Blueprint $table)
        {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('device_id')->unsigned();
            $table->foreign('device_id')->references('id')->on('devices');
        });

        Schema::create('device_capabilities', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('unit');
            $table->string('data_type');

            $table->timestamps();

            $table->integer('device_id')->unsigned();
            $table->foreign('device_id')->references('id')->on('devices');
        });

        Schema::create('devices_missions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('device_uuid');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->dateTime('registration_date')->nullable();
            $table->timestamps();

            $table->integer('device_id')->unsigned();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->integer('mission_id')->unsigned();
            $table->foreign('mission_id')->references('id')->on('missions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices_missions');
        Schema::dropIfExists('users_devices');
        Schema::dropIfExists('device_capabilities');
        Schema::dropIfExists('devices');
    }
}
