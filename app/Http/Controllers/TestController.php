<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Descriptions\MissionType;

class TestController extends Controller {

    public function test() {

        return MissionType::all();
    }
}
