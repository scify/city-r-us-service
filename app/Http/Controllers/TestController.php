<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Descriptions\MissionType;
use App\Services\Radical\RadicalIntegrationManager;

class TestController extends Controller {

    private $radicalIntegrationManager;

    public function __construct() {

        $this->radicalIntegrationManager = new RadicalIntegrationManager();
    }

    public function test() {

        return 'a';

        return $this->radicalIntegrationManager->getApiKey();
    }
}
