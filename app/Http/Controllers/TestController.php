<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Descriptions\MissionType;
use App\Services\Radical\RadicalIntegrationManager;
use App\Services\UserService;

class TestController extends Controller {

    private $radicalIntegrationManager;
    private $userService;

    public function __construct() {

        $this->radicalIntegrationManager = new RadicalIntegrationManager();
        $this->userService = new UserService();
    }

    public function test() {

      /*  \Mail::raw('Text to e-mail', function ($message) {
            //
        });*/
    }
}
