<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Descriptions\MissionType;
use App\Services\Radical\RadicalConfigurationAPI;

class TestController extends Controller {

    private $radicalConfigucationAPI;

    public function __construct() {

        $this->radicalConfigucationAPI = new RadicalConfigurationAPI();
    }

    public function test() {

        return $this->radicalConfigucationAPI->getApiKey();
    }
}
