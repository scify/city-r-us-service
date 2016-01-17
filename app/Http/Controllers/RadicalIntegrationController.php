<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ApiResponse;
use App\Models\Mission;
use App\Services\Radical\RadicalIntegrationManager;

class RadicalIntegrationController extends Controller {

    private $radicalIntegrationManager;

    public function __construct() {

        $this->radicalIntegrationManager = new RadicalIntegrationManager();
    }

    /**
     * Integration with Radical API.
     * Getting all observations and measurements for a certain mission,
     * based on the device uuid and filtered by date.
     *
     * @param $missionId
     * @return array
     */
    public function getObservations($missionId) {

        $response = new ApiResponse();

        if ($missionId != null) {

            $mission = Mission::with('devices')->find($missionId);

            if ($mission == null) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'mission_null',
                    'description' => 'The mission was not found'];

                return \Response::json($response);
            }

            //for each device, get its observations
            foreach ($mission->devices as $device) {

                $deviceUUID = env('RADICAL_CITYNAME') . '.' . $mission->radical_service_id . '.' . $device->device_uuid;
                $obs = $this->radicalIntegrationManager->getMeasurementsByDeviceUUID($deviceUUID);
                $device->measurements = json_decode($obs);
            }

            $response->status = 'success';
            $response->message = [
                'mission' => $mission];

            return \Response::json($response);

        } else {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_id_null',
                'description' => 'The missionId should not null'];

            return \Response::json($response);
        }
    }

    public function getObservationsByDate($missionId, $date) {

        $response = new ApiResponse();

        if ($missionId != null) {

            $mission = Mission::with('devices')->find($missionId);

            if ($mission == null) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'mission_null',
                    'description' => 'The mission was not found'];

                return \Response::json($response);
            }

            $timestamp = \Carbon::parse($date)->timestamp;

            //for each device, get its observations
            foreach ($mission->devices as $device) {

                $deviceUUID = env('RADICAL_CITYNAME') . '.' . $mission->radical_service_id . '.' . $device->device_uuid;
                $obs = $this->radicalIntegrationManager->getMeasurementsByDeviceUUID($deviceUUID, $timestamp);
                $device->measurements = json_decode($obs);
            }

            $response->status = 'success';
            $response->message = [
                'mission' => $mission];

            return \Response::json($response);

        } else {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_id_null',
                'description' => 'The missionId should not null'];

            return \Response::json($response);
        }
    }
}
