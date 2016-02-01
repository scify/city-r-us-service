<?php

use App\Models\Device as Device;
use App\Models\User as User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     * Use php artisan db:seed to run the seed files.
     *
     * @return void
     */
    public function run() {
        $user = User::create([
            'name' => 'admin',
            'email' => 'test@scify.org',
            'password' => Hash::make('1q2w3e'),
        ]);


        Device::create([
            'device_uuid' => 'test',
            'model' => 'test',
            'manufacturer' => 'test',
            'user_id' => $user->id
        ]);

        $user->roles()->attach([1]);

    }

}
