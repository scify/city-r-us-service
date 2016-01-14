<?php namespace App\Services;

use App\Models\ApiResponse;
use App\Models\Device;
use App\Services\Radical\RadicalIntegrationManager;

class DeviceService {

    private $radicalIntegrationManager;

    public function __construct() {
        $this->radicalIntegrationManager = new RadicalIntegrationManager();
    }


    /**
     * Check if a device is register for a certain mission
     *
     * @param $missionName
     * @param $deviceName
     */
    public function isRegistered($missionId, $deviceUUID) {
        $device = Device::where('device_uuid', $deviceUUID)
            ->whereHas('missions', function ($q) use ($missionId) {
                $q->where('mission_id', $missionId);
            })->first();

        if ($device == null)
            return false;

        return true;
    }

    /**
     * Store device to our db
     *
     */
    public function store($userId, $device = null) {

        if ($device == null) {
            $device = new Device([
                'device_uuid' => \Request::get('device_uuid'),
                'model' => \Request::get('model'),
                'manufacturer' => \Request::get('manufacturer'),
                'user_id' => $userId,
            ]);
        }

        $device->save();

        return $device;
    }

    /**
     * Store device to radical
     *
     * @param $device
     * @return mixed
     */
    public function registerToRadical($device) {
        return $this->radicalIntegrationManager->registerDevice($device);
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
