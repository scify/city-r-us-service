<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\Radical\Curl;
use Illuminate\Http\Request;
use Swagger\Annotations\Response;

class MapController extends Controller
{

    private $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }


    /**
     * Return venues
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get venues",
     *     path="/map/venues",
     *     description="Get venues from Radical",
     *     operationId="api.map.venues",
     *     produces={"application/json"},
     *     tags={"map"},
     *     @SWG\Parameter(
     *       name="lat",
     *       description="Latitude",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="lon",
     *       description="Longitude",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns venues"
     *     )
     * )
     */
    public function getVenues(){
        $response = $this->curl->get("http://athens.radical-project.eu:8080/Radical/rest/dataapi/getVenues", [
            "lat" => \Request::get("lat"),
            "lon" => \Request::get("lon"),
            "sns" => "foursquare"]);
        if((array)json_decode($response)->response->venues== null || (array)json_decode($response)->response->venues == []){
            return ["No venues found"];
        } else{
            return (array)json_decode($response)->response->venues;
        }
    }

    /**
     * Return Events
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get events",
     *     path="/map/events",
     *     description="Get events from Radical",
     *     operationId="api.map.events",
     *     produces={"application/json"},
     *     tags={"map"},
     *     @SWG\Parameter(
     *       name="lat",
     *       description="Latitude",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="lon",
     *       description="Longitude",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns events"
     *     )
     * )
     */
    public function getEvents(){
        $response = $this->curl->get("http://athens.radical-project.eu:8080/Radical/rest/dataapi/getSnsEvents", [
            "sns"  =>"eventful",
            "lat"  => \Request::get("lat"),
            "lon"  => \Request::get("lon"),
            "rad"  => "2",
            "page" => "2"]);
        if((array)json_decode($response)->events ==null){
            return ["No events found"];
        } else{
            return (array)json_decode($response)->events->event;
        }
    }
}
