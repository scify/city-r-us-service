<?php namespace App\Services\Radical;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/11/2015
 * Time: 3:48 μμ
 */
class RadicalIntegrationManager {

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
        return $this->sendMissionRequest($mission, false);
    }

    public function deleteMission($mission) {
        $url = env("RADICAL_CONFIGURATION_API") . "cities/" . env("RADICAL_CITYNAME") . "/services?api_key=" . $this->getApiKey();

        $params = array("Service_ID" => $mission->radical_service_id,
            "City_ID" => 'ATHENS');

        $response = $this->curl->delete($url . "/" . $mission->radical_service_id, $params, true);
        return $response;
    }

    private function sendMissionRequest($mission, $missionIsNew) {
        $url = env("RADICAL_CONFIGURATION_API") . "cities/" . env("RADICAL_CITYNAME") . "/services";
        $params = array("Service_ID" => $mission->radical_service_id,
            "Service_Description" => $mission->description);
        if ($missionIsNew)
            $this->curl->post($url, $params, true);
        else
            return $this->curl->put($url . "/" . $mission->radical_service_id, $params, true);
    }


    public function registerDevice($device) {
        $apiKey = $this->getApiKey();

        $url = env("RADICAL_REPOSITORY_API") . "registerDevice?api_key=" . $apiKey;

        $params = $device;

        $response = $this->curl->post($url, $params, true);

        return $response;
    }

    public function storeObservation($observation) {
        $apiKey = $this->getApiKey();

        $url = env("RADICAL_REPOSITORY_API") . "registerObservation?api_key=" . $apiKey;

        $params = $observation;

        $response = $this->curl->post($url, $params, true);

        return $response;
    }


    public function getMeasurementsByDeviceUUID($observationId){

        $apiKey = $this->getApiKey();

        $url = env("RADICAL_DATA_API") . "getMeasurementsByDeviceId?key=" . $apiKey .'&id=' . $observationId . '&format=json';

        $measurements = $this->curl->get($url, [], true);

        return $measurements;

    }

    public function getMeasurementsByDate($observationId, $date){

        $apiKey = $this->getApiKey();

        //getMeasurementsFromDate (String id, long date, String format, String key)
        $url = env("RADICAL_DATA_API") . "getMeasurementsFromDate?key=" . $apiKey .'&id=' . $observationId . '$date=' . $date . '&format=json';

        $measurements = $this->curl->get($url, [], true);

        return $measurements;
    }
} 
