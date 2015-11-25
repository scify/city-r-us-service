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
            $table->string('device_uuid')->unique();
            $table->string('model');
            $table->string('manufacturer');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('type');
            $table->string('status');
            $table->dateTime('registration_date')->nullable();

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

            $table->string('device_uuid');
            $table->foreign('device_uuid')->references('device_uuid')->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_devices');
        Schema::dropIfExists('device_capabilities');
        Schema::dropIfExists('devices');
    }
}
