<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ApiResponse;
use App\Models\Mission;
use App\Models\Observation;
use App\Models\User;
use App\Services\ObservationService;
use App\Services\PointService;
use App\Services\Radical\RadicalConfigurationAPI;

class ObservationController extends Controller {

    private $mission;
    private $radicalServiceConfiguration;
    private $observationService;
    private $pointService;

    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['store', 'update', 'destroy']]);
        $this->radicalServiceConfiguration = new RadicalConfigurationAPI();
        $this->observationService = new ObservationService();
        $this->pointService = new PointService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get all observations",
     *     path="/missions",
     *     description="Retrieve all the oservations.",
     *     operationId="api.observations",
     *     produces={"application/json"},
     *     tags={"observations"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns all the observations of the application"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function index() {
        $observations = Observation::get();

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'observations' => $observations];

        return \Response::json($response);
    }


    /**
     * Store an observation.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Store a new observation",
     *     path="/observations/store",
     *     description="Create and save a new observation.",
     *     operationId="api.observations",
     *     produces={"application/json"},
     *     tags={"observations"},
     *      @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header, in order to authenticate the user making the call. Format should be: Authorization: Bearer x.y.z",
     *       required=true,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *     @SWG\Parameter(
     *        name="device_uuid",
     *        description="The uuid of the device",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *        name="mission_id",
     *        description="The id of the chosen mission",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="latitude",
     *       description="",
     *       required=true,
     *       default="",
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="longitude",
     *       description="",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="observation_date",
     *       description="The date of the observation. It must follow the format Y-m-d hh:mm:ss.",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="measurements",
     *       description="An array of measurements. Should be in the following format: { 'measurements' : [ { 'latitude': 2.45,  'longitude': 0.90,  'observation_date': '2015-12-12 12:12:12'  }  ] }",
     *       required=true,
     *       in="body",
     *       @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/measurement")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the id of the observation created",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/observation")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function store() {
        $response = new ApiResponse();

        //Save the observation
        $observation = $this->observationService->store();

        if ($observation->status != 'error') {

            //reward user for their submission
            $points = $this->pointService->observationReward();

            $response->status = 'success';
            $response->message = [
                'observation' => $observation,
                'points' => $points
            ];
        } else {
            $response = $observation;
        }

        return \Response::json($response);
    }
}
