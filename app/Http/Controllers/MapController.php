<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Services\Radical\Curl;

class MapController extends Controller {


    private $curl;

    function __construct() {
        $this->curl = new Curl();
    }


    public function getVenues() {

        return 'skassa';
        $response = $this->curl->get("http://athens.radical-project.eu:8080/Radical/rest/dataapi/getVenues", [
            "lat" => \Request::get("lat"),
            "lon" => \Request::get("lon"),
            "sns" => "foursquare"]);

        return (array)json_decode($response)->response->venues;
    }



}
