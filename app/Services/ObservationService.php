<?php namespace App\Services;

use App\Models\ApiResponse;
use App\Models\Device;
use App\Models\Measurement;
use App\Models\Mission;
use App\Models\Observation;
use App\Services\Radical\RadicalIntegrationManager;

class ObservationService {

    private $deviceService;
    private $mission;
    private $radicalIntegrationManager;

    public function __construct() {
        $this->deviceService = new DeviceService();
        $this->radicalIntegrationManager = new RadicalIntegrationManager();
    }

    /*
     * Store an observation and its measurements
     */
    public function store() {

        //TODO: maybe the name of the device is not needed -> retrieve it from jwt

        $responseObs = $this->validateObservation();

        if ($responseObs->status == 'error') {

            return $responseObs;

        } else {
            $device = Device::where('device_uuid', \Request::get('device_uuid'))->first();

            //first check if the device is registered for this mission to radical
            //for our db, that means that there's a row in devices_missions table
            if (!$this->deviceService->isRegistered(\Request::get('mission_id'), \Request::get('device_uuid'))) {

                if ($this->mission == null)
                    $this->mission = Mission::find(\Request::get('mission_id'));

                if (!\Request::has('latitude') || \Request::get('latitude') == '')
                    $latitude = number_format(0, 6);
                else
                    $latitude = number_format(\Request::get('latitude'), 6);

                if (!\Request::has('longitude') || \Request::get('longitude') == '')
                    $longitude = number_format(0, 6);
                else
                    $longitude = number_format(\Request::get('longitude'), 6);

                //first create a row in devices_missions table
                $device->missions()->attach($this->mission->id, [
                    'device_uuid' => env('RADICAL_CITYNAME') . '.' . $this->mission->radical_service_id . '.' . $device->device_uuid,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'registration_date' => date('Y-m-d H:i:s')
                ]);

                $this->mission->users()->attach(\Auth::user()->id);

                //then send data to radical api
                $tmp_device = [
                    'Device_UUID' => env('RADICAL_CITYNAME') . '.' . $this->mission->radical_service_id . '.' . $device->device_uuid,
                    'Model' => $device->model,
                    'Manufacturer' => $device->manufacturer,
                    'Latitude' => $latitude,
                    'Longitude' => $longitude,
                    'Type' => $device->type,
                    'Status' => intval($device->status),
                    'Registration_Date' => date('Y-m-d H:i:s'),
                ];

                $this->deviceService->registerToRadical($tmp_device);
            }


            $responseMeas = $this->validateMeasurements(\Request::get('measurements'));

            if ($responseMeas->status == 'error') {
                return \Response::json($responseMeas);
            }

            if ($this->mission == null)
                $this->mission = Mission::find(\Request::get('mission_id'));


            $observation = new Observation([
                'device_uuid' => env('RADICAL_CITYNAME') . '.' . $this->mission->radical_service_id . '.' . $device->device_uuid,
                'latitude' => \Request::get('latitude'),
                'longitude' => \Request::get('longitude'),
                'observation_date' => \Request::get('observation_date'),
                'device_id' => $device->id
            ]);

            //save new observation to the db
            $observation->save();
            $radicalMeasurements = $this->getMeasurements($observation->id);

            $radicalObservation = ([
                'Device_UUID' => env('RADICAL_CITYNAME') . '.' . $this->mission->radical_service_id . '.' . $device->device_uuid,
                'Latitude' => \Request::get('latitude'),
                'Longitude' => \Request::get('longitude'),
                'Observation_Date' => \Request::get('observation_date'),
                'Measurements' => $radicalMeasurements,
            ]);

            $this->radicalIntegrationManager->storeObservation($radicalObservation);
            return $observation;
        }

    }

    /**
     * Save the measurements to our db
     * and create an array of measurements to sent to the radical API
     *
     * @param $observation_id
     * @return array
     */
    private function getMeasurements($observation_id) {

        $type = 'test';
        $value = 'test';
        $unit = 'test';

        if ($this->mission != null) {
            $type = $this->mission->type->name;
        }

        $radicalMeasurements = [];
        foreach (\Request::get('measurements') as $measurement) {

            $meas = new Measurement([
                'type' => $type,
                'value' => $value,
                'unit' => $unit,
                'latitude' => $measurement['latitude'],
                'longitude' => $measurement['longitude'],
                'observation_id' => $observation_id,
                'observation_date' => $measurement['observation_date']]);

            $meas->save();

            array_push($radicalMeasurements, [
                'Latitude' => $meas->latitude,
                'Longitude' => $meas->longitude,
                'Type' => $meas->type,
                'Value' => $meas->value,
                'Unit' => $meas->unit,
                'Observation_Date' => $meas->observation_date,
            ]);
        }

        return $radicalMeasurements;
    }

    /**
     * Validate the observation data before performing any action
     *
     */
    private function validateObservation() {

        $response = new ApiResponse();

        if (!\Request::has('device_uuid')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'device_uuid_null',
                'description' => 'The device uuid should not be null'];
        }
        else{
            $device = Device::where('device_uuid', \Request::get('device_uuid'))->first();

            if($device==null){
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'device_not_found',
                    'description' => 'The device could not be found'];
            }
        }

        if (!\Request::has('mission_id')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_id_null',
                'description' => 'The mission id should not be null'];
        } else {
            //check that the mission_id exists
            $this->mission = Mission::with('type')->find(\Request::get('mission_id'));

            if ($this->mission == null) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'mission_id_not_found',
                    'description' => 'The requested mission could not be found'];
            }
        }

        if (\Request::has('observation_date')) {
            if (!$this->validateDate(\Request::get('observation_date'))) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'wrong_date_format',
                    'description' => 'The date should be in the following format: Y-m-d hh:mm:ss'];
            }
        }

        if (\Request::has('latitude') && !is_numeric(\Request::get('latitude')) || \Request::has('longitude') && !is_numeric(\Request::get('longitude'))) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'coordinates_not_numeric',
                'description' => 'The coordinates of the observation should be numeric'];
        }

        if (!\Request::has('measurements')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'measurements_null',
                'description' => 'The measurements should not be null'];
        } else {
            foreach (\Request::get('measurements') as $measurement) {

                if (!isset($measurement['latitude']) || $measurement['latitude'] == '' || !isset($measurement['longitude']) || $measurement['longitude'] == '') {
                    $response->status = 'error';
                    $response->message = [
                        'id' => '',
                        'code' => 'coordinates_null',
                        'description' => 'The coordinates of the measurements should not be null'];
                } else if (!is_numeric($measurement['latitude']) || !is_numeric($measurement['longitude'])) {
                    $response->status = 'error';
                    $response->message = [
                        'id' => '',
                        'code' => 'coordinates_not_numeric',
                        'description' => 'The coordinates of the measurements should be numeric'];
                }
            }
        }

        return $response;
    }

    /**
     * Validate the observation data before performing any action
     *
     */
    private function validateMeasurements($measurements) {

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [];

        foreach ($measurements as $measurement) {

            if ($measurement['observation_date'] != null) {
                if (!$this->validateDate($measurement['observation_date'])) {
                    $response->status = 'error';
                    $response->message = [
                        'id' => '',
                        'code' => 'wrong_date_format',
                        'description' => 'The date should be in the following format: Y-m-d hh:mm:ss'];

                    break;
                }
            } else {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'date_null',
                    'description' => 'The measurement date should not be null'];

                break;
            }

            if (!is_numeric($measurement['latitude']) || !is_numeric($measurement['longitude'])) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'coordinates_not_numeric',
                    'description' => 'The coordinates of the measurements should be numeric'];

                break;
            }
        }
        return $response;
    }


    private function validateDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $d && $d->format('Y-m-d H:i:s') == $date;
    }

}
