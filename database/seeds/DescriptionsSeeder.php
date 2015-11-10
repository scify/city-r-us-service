<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DescriptionsSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mission_types')->delete();

        $types = [
            ['name' => 'location'],
            ['name' => 'route'],
        ];

        DB::table('mission_types')->insert($types);
    }
}
