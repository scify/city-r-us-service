<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ApiResponse;
use App\Models\Measurement;
use App\Models\Mission;
use App\Models\Observation;
use App\Services\ObservationService;
use App\Services\Radical\RadicalConfigurationAPI;

class ObservationController extends Controller {

    private $mission;
    private $radicalServiceConfiguration;
    private $observationService;

    public function __construct() {
      //  $this->middleware('jwt.auth', ['only' => ['store', 'update', 'destroy']]);
        $this->radicalServiceConfiguration = new RadicalConfigurationAPI();
        $this->observationService = new ObservationService();
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
     *       required=false,
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
     *       required=false,
     *       default=" ",
     *       type="number",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="longitude",
     *       description="",
     *       required=false,
     *       type="number",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="observation_date",
     *       description="The date of the observation. It must follow the format y-m-d.",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="measurements",
     *       description="An array of measurements.",
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

        return $this->observationService()->store();
    }


}
