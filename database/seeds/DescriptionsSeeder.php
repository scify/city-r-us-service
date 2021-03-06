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
        //User roles
        DB::table('roles')->delete();

        $types = [
            [
                'id' => 1,
                'name' => 'web'
            ],
            [
                'id' => 2,
                'name' => 'mobile'
            ]
        ];

        DB::table('roles')->insert($types);


        //Mission types
        DB::table('mission_types')->delete();

        $types = [
            [
                'id' => 1,
                'name' => 'location'
            ],
            [
                'id' => 2,
                'name' => 'route'
            ]
        ];

        DB::table('mission_types')->insert($types);


    }
}
