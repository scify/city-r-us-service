<?php namespace App\Services;

use App\Models\ApiResponse;
use App\Models\Device;
use App\Services\Radical\RadicalConfigurationAPI;

class DeviceService {

    private $radicalServiceConfiguration;

    public function __construct() {
        $this->radicalServiceConfiguration = new RadicalConfigurationAPI();
    }


    public function checkStatus($missionName, $deviceName) {

        $device = Device::where('device_uuid', ENV('RADICAL_CITYNAME') . $missionName . $deviceName)->first();

        if ($device == null) {
           $this->radicalServiceConfiguration->registerDevice(null);
        }

        return;
    }

    /**
     * Store device to our db
     *
     */
    public function store(){

        $device = new Device([
            'device_name' => \Request::get('device_name'),
            'model' => \Request::get('model'),
            'manufacturer' => \Request::get('manufacturer'),
            'latitude' => \Request::get('latitude'),
            'longitude' => \Request::get('longitude'),
            'type' => \Request::get('type'),
            'status' => \Request::get('status'),
        ]);

        $device->save();

        return $device;
    }

    /**
     * Store device to our db and to radical
     *
     * @param $device
     * @return mixed
     */
    public function registerDevice($device){
        $device = new Device([
            'device_uuid' => env('RADICAL_CITYNAME') . '.' . $this->mission->name . '.' . \Request::get('device_name'),
            'model' => $device['model'],
            'manufacturer' => $device['manufacturer'],
            'registration_date' => date('Y-m-d H:i:s')
        ]);

        $device->save();

        $radicalDevice = ([
            'Device_UUID' => $device->device_uuid,
            'Model' => $device->model,
            'Manufacturer' => $device->manufacturer,
            'Latitude' => floatval($device->latitude),
            'Longitude' => floatval($device->longitude),
            'Type' =>  $device->type,
            'Status' => intval($device->status),
            'Registration_Date' => date('Y-m-d H:i:s'),
        ]);

        return $this->radicalServiceConfiguration->registerDevice($radicalDevice);
    }



    /**
     * Validate the device data before saving to db
     * @return ApiResponse
     */
    public function validateDevice() {

        $response = new ApiResponse();

        if (!\Request::has('device_name')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'device_name_null',
                'description' => 'The device name should not be null'];
        }

        if (!\Request::has('model')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'model_null',
                'description' => 'The device model should not be null'];
        }
        if (!\Request::has('manufacturer')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'manufacturer_null',
                'description' => 'The device manufacturer should not be null'];
        }
        if (!\Request::has('type')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'type_null',
                'description' => 'The device type should not be null'];
        }

        if (!\Request::has('status')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'status_null',
                'description' => 'The device status should not be null'];
        } else if (intval(\Request::has('status')) != 0 && intval(\Request::has('status')) != 1) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'status_error',
                'description' => 'The device status should be either 0 (inactive) or 1 (active)'];

        }

        if (\Request::has('latitude') && !is_numeric(\Request::get('latitude')) || \Request::has('longitude') && !is_numeric(\Request::get('longitude'))) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'coordinates_not_numeric',
                'description' => 'The coordinates of the device should be numeric'];
        }

        if (!\Request::has('mission_id')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_id_null',
                'description' => 'The mission id should not be null'];
        } else {
            //check that the mission_id exists
            $this->mission = Mission::find(\Request::get('mission_id'));

            if ($this->mission == null) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'mission_id_not_found',
                    'description' => 'The requested mission could not be found'];
            }
        }

        if (\Request::has('registration_date')) {
            if (!$this->validateDate(\Request::get('registration_date'))) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'wrong_date_format',
                    'description' => 'The date should be in the following format: Y-m-d hh:mm:ss'];
            }
        }

        return $response;
    }


    private function validateDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $d && $d->format('Y-m-d H:i:s') == $date;
    }
}
