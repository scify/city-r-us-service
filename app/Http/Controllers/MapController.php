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
    public function getVenues()
    {
//        return "test";
        $response = $this->curl->get("http://athens.radical-project.eu:8080/Radical/rest/dataapi/getVenues", [
            "lat" => \Request::get("lat"),
            "lon" => \Request::get("lon"),
            "sns" => "foursquare"]);
        return (array)json_decode($response)->response->venues;
    }

}
