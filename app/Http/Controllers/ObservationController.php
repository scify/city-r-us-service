<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ApiResponse;
use App\Models\Measurement;
use App\Models\Mission;
use App\Models\Observation;

class ObservationController extends Controller {

    private $mission;


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

        //TODO: maybe the name of  the device is not needed -> retrieve it from jwt
        //TODO: we must also check that jwt doesn't expire as often on mobile as it does on web

        $response = $this->validateData();

        if ($response->status == 'error') {

            return \Response::json($response);

        } else {

            $observation = new Observation([
                'device_uuid' => env('RADICAL_CITYNAME') . '.' . \Request::get('mission_id') . '.' . \Request::get('device_uuid'),
                'latitude' => \Request::get('latitude'),
                'longitude' => \Request::get('longitude'),
                'observation_date' => \Request::get('observation_date'),
            ]);

            //save new observation to the db
            $observation->save();
            $radicalMeasurements = $this->getMeasurements($observation->id);

            $radicalObservation = ([
                'Observation_Id' => $observation->id,
                'Device_UUID' => env('RADICAL_CITYNAME') . '.' . \Request::get('mission_id') . '.' . \Request::get('device_uuid'),
                'Latitude' => \Request::get('latitude'),
                'Longitude' => \Request::get('longitude'),
                'Observation_Date' => \Request::get('registration_date'),
                'Measurements' => $radicalMeasurements,
            ]);

            //return $this->radicalConfigurationAPI->registerDevice($radicalObservation);
            return $observation;
        }
    }

    /**
     * Validate the observation data before performing any action
     *
     */
    private function validateData() {

        $response = new ApiResponse();

        if (!\Request::has('device_uuid')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'device_uuid_null',
                'description' => 'The device uuid should not be null'];
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
     * Save the measurements to our db
     * and create an array of measurements to sent to the radical API
     *
     * @param $observation_id
     * @return array
     */
    private function getMeasurements($observation_id) {

        $type = '';
        $value = '';
        $unit = '';

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
                'Measurement_ID' => $meas->id,
                'Observation_Id' => $observation_id,
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
}
