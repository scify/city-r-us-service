<?php namespace App\Services\Radical;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/11/2015
 * Time: 3:48 μμ
 */
class RadicalConfigurationAPI {

    private $curl;

    function __construct() {
        $this->curl = new Curl();
    }


    public function getApiKey() {

        $url = env("RADICAL_DATA_API") . 'getApiKey?name=' . env('RADICAL_NAME') . '&password=' . env('RADICAL_PASSWORD');

        $apiKey = $this->curl->get($url, []);

        return $apiKey;
    }

    public function registerMission($mission) {
        $this->sendRequest($mission, true);
    }

    public function updateMission($mission) {
        $this->sendRequest($mission, false);
    }

    private function sendRequest($mission, $missionIsNew) {
        $url = env("RADICAL_CONFIGURATION_API") . "cities/" . env("RADICAL_CITYNAME") . "/services";
        $params = array("Service_ID" => $mission->radical_service_id,
            "Service_Description" => $mission->name . "\n" . $mission->description);
        if ($missionIsNew)
            $this->curl->post($url, $params, true);
        else
            $this->curl->put($url . "/" . $mission->radical_service_id, $params, true);
    }
} 
