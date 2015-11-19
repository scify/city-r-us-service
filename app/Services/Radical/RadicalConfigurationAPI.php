<?php namespace App\Services\Radical;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/11/2015
 * Time: 3:48 μμ
 */

class RadicalConfigurationAPI {

    const  ServiceIdTemplate = "city_r_us_mission_%1";

    private $curl;
    function __construct()
    {
        $this->curl = new Curl();
    }

    public function registerMission($mission){
        $this->sendRequest($mission,true);
    }

    public function updateMission($mission){
        $this->sendRequest($mission,false);
    }

    private function sendRequest($mission,$missionIsNew)
    {
        try {
            $url = env("RADICAL_CONFIGURATION_API") . "/cities/" . env("RADICAL_CITYNAME") . "/services";
            $params = array("Service_ID" => sprintf(RadicalConfigurationAPI::ServiceIdTemplate, $mission->id),
                "Service_Description" => $mission->name . "\n" . $mission->description);
            if ($missionIsNew)
                $this->curl->post($url, $params, true);
            else
                $this->curl->put($url, $params, true);

        } catch (Exception $e) {
            Log::error($e);
        }
    }
} 
