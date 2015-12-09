<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ApiResponse;
use App\Models\Mission;
use App\Services\DeviceService;
use App\Services\Radical\RadicalConfigurationAPI;

class DeviceController extends Controller {

    private $mission;
    private $radicalConfigurationAPI;
    private $deviceService;

    /* only for test purposes */
    private $deviceModels = ['iPhone', 'iPad', 'GT-I9100', 'modelXYZ', 'Nexus', 'Lenovo A8-50'];
    private $deviceManufacturers = ['Apple', 'Samsung', 'Nokia', 'Google', 'Lenovo'];


    public function __construct() {
        $this->radicalConfigurationAPI = new RadicalConfigurationAPI();
        $this->$deviceService = new DeviceService();
    }

    /**
     * Register a new device
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post1(
     *     summary="Register a new device",
     *     path="/devices/register",
     *     description="Register a device to the Radical API and save it to db",
     *     operationId="api.devices",
     *     produces={"application/json"},
     *     tags={"devices"},
     *      @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header, in order to authenticate the user making the call. Format should be: Authorization: Bearer x.y.z",
     *       required=false,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *     @SWG\Parameter(
     *        name="mission_id",
     *        description="The id of the mission that the user wants to participate to",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="device_name",
     *       description="The name of the device",
     *       required=true,
     *       default="",
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="model",
     *       description="The model of the device",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="manufacturer",
     *       description="The manufacturer of the device",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),     *
     *     @SWG\Parameter(
     *       name="latitude",
     *       description="The latitude of the device",
     *       required=true,
     *       type="number",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="longitude",
     *       description="The longitude of the device",
     *       required=true,
     *       type="number",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="type",
     *       description="Examples: 'antenna', 'smartphone', 'sensor', ... ",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="status",
     *       description="Valid values '0' (inactive) or '1' (active),",
     *       required=true,
     *       type="integer",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function register() {

        $response = $this->deviceService->validateDevice();

        if ($response->status != 'error')
            $response = $this->deviceService->registerDevice(\Request::all());

        return \Response::json($response);
    }
}
