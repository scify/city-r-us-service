<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Device;
use App\Services\Radical\RadicalConfigurationAPI;

class DeviceController extends Controller {

    private $radicalConfigurationAPI;

    /* only for test purposes */
    private $deviceModels = ['iPhone', 'iPad', 'GT-I9100', 'modelXYZ', 'Nexus', 'Lenovo A8-50'];
    private $deviceManufacturers = ['Apple', 'Samsung', 'Nokia', 'Google', 'Lenovo'];


    public function __construct() {
        $this->radicalConfigurationAPI = new RadicalConfigurationAPI();
    }

    /**
     * Register a new device
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
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
     *        name="missionId",
     *        description="The id of the mission that the user wants to participate to",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="deviceName",
     *       description="The name of the device",
     *       required=true,
     *       default=" ",
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

        if (!\Request::has('deviceName')) {

            $serviceId = 'cityrus_illegal-parking';
            $device_name = 'test-' . str_random(4);

            //device_uui = CityID.ServiceId.DeviceName

            $device = new Device([
                'device_uuid' => env('RADICAL_CITYNAME') . '.' . $serviceId . '.' . $device_name,
                'model' => $this->deviceModels[array_rand($this->deviceModels)],
                'manufacturer' => $this->deviceManufacturers[array_rand($this->deviceManufacturers)],
                'latitude' => 56.560000,
                'longitude' => 10.560000,
                'type' => 'RFID antenna',
                'status' => 1,
                'registration_date' => date('Y-m-d H:i:s'),
            ]);

            $radicalDevice = ([
                'Device_UUID' => env('RADICAL_CITYNAME') . '.' . $serviceId . '.' . $device_name,
                'Model' => $this->deviceModels[array_rand($this->deviceModels)],
                'Manufacturer' => $this->deviceManufacturers[array_rand($this->deviceManufacturers)],
                'Latitude' => 56.560000,
                'Longitude' => 10.560000,
                'Type' => 'RFID antenna',
                'Status' => 1,
                'Registration_Date' => date('Y-m-d H:i:s'),
            ]);

        } else {

            $device = new Device([
                'device_uuid' => env('RADICAL_CITYNAME') . '.' . \Request::get('missionId') . '.' . \Request::get('deviceName'),
                'model' => \Request::get('model'),
                'manufacturer' => \Request::get('manufacturer'),
                'latitude' => \Request::get('latitude'),
                'longitude' => \Request::get('longitude'),
                'type' => \Request::get('type'),
                'status' => \Request::get('status'),
                'registration_date' => date('Y-m-d H:i:s'),
            ]);

            $radicalDevice = ([
                'Device_UUID' => env('RADICAL_CITYNAME') . '.' . \Request::get('missionId') . '.' . \Request::get('deviceName'),
                'Model' => \Request::get('model'),
                'Manufacturer' => \Request::get('manufacturer'),
                'Latitude' => \Request::get('latitude'),
                'Longitude' => \Request::get('longitude'),
                'Type' => \Request::get('type'),
                'Status' => \Request::get('status'),
                'Registration_Date' => date('Y-m-d H:i:s'),
            ]);
        }

        $device->save();
        return $this->radicalConfigurationAPI->registerDevice($radicalDevice);
    }

}
