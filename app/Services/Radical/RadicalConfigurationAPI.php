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

    /**
     * Get the API key from radical, based on a username and password
     *
     * @return mixed
     */
    public function getApiKey() {

        $url = env("RADICAL_DATA_API") . 'getApiKey?name=' . env('RADICAL_NAME') . '&password=' . env('RADICAL_PASSWORD');

        $apiKey = $this->curl->get($url, [], true);

        return $apiKey;
    }

    public function registerMission($mission) {
        $this->sendMissionRequest($mission, true);
    }

    public function updateMission($mission) {
        $this->sendMissionRequest($mission, false);
    }

    private function sendMissionRequest($mission, $missionIsNew) {
        $url = env("RADICAL_CONFIGURATION_API") . "cities/" . env("RADICAL_CITYNAME") . "/services";
        $params = array("Service_ID" => $mission->radical_service_id,
            "Service_Description" => $mission->name . "\n" . $mission->description);
        if ($missionIsNew)
            $this->curl->post($url, $params, true);
        else
            $this->curl->put($url . "/" . $mission->radical_service_id, $params, true);
    }


    public function registerDevice($device) {
        $apiKey = $this->getApiKey();

        $url = env("RADICAL_REPOSITORY_API") . "registerDevice?api_key=".$apiKey;

        $params = $device;

        $response = $this->curl->post($url, $params, true);

        return $response;
    }
} 
