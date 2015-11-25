<?php namespace App\Services;


use App\Exceptions\RadicalApiException;
use App\Models\ApiResponse;
use App\Models\Descriptions\MissionType;
use App\Models\Mission;
use App\Services\Radical\RadicalConfigurationAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MissionService {

    private $radicalServiceConfiguration;

    function __construct() {
        $this->radicalServiceConfiguration = new RadicalConfigurationAPI();
    }

    public function store($data) {

        $response = new ApiResponse();
        $status = 200;

        //check if there are other missions with the same radical_service_id
        $missions = Mission::where('radical_service_id', $data['radical_service_id'])->get();

        //Validations
        if ($data['name'] == null || $data['name'] == '') {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The mission\'s name should not be null or an empty string.'];

            $status = 400; //todo: for errors use 500?
        } else if ($data['radical_service_id'] == null || $data['radical_service_id'] == '') {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The mission\'s id should not be null or an empty string.'];

            $status = 400;
        }
        else if (sizeof($missions)>0) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The mission\'s radical_service_id already exists'];

            $status = 400;
        }
        else {

            $mission = $this->sanitize($data);
            $mission->radical_service_id = "cityrus_" . Str::random();
            try {
                $this->radicalServiceConfiguration->registerMission($mission);
                $mission->save();
                $response->status = 'success';
                $response->message = $mission->id;
            } catch (RadicalApiException $e) {
                Log::error($e);
                $response->status = 'error';
                $response->message = $e->getMessage();
                $status = 500;
            }
        }
        return \Response::json($response, $status);
    }


    public function update($data) {

        $response = new ApiResponse();
        $status = 200;

        $mission = Mission::find(\Request::get('id'));

        if ($mission == null) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_not_found',
                'description' => 'The mission was not found'];
            $status = 400;
        } else {
            $mission = $this->sanitize($data, $mission);
            try {
                $this->radicalServiceConfiguration->updateMission($mission);
                $mission->save();
                $response->status = 'success';
                $response->message = $mission->id;
            } catch (RadicalApiException $e) {
                Log::error($e);
                $response->status = 'error';
                $response->message = $e->getMessage();
                $status = 500;
            }
        }
        return \Response::json($response, $status);
    }

    /**
     * Sanitize data before saving
     *
     * @param $data
     * @return \App\Models\Mission|null|string
     */
    private function sanitize($data, $mission = null) {

        if ($mission == null)
            $mission = new Mission();

        if (isset($data['name']))
            $mission->name = $data['name'];

        if (isset($data['img_name']))
            $mission->img_name = $data['img_name'];


        if (isset($data['description']))
            $mission->description = $data['description'];
        else
            $mission->description = '';

        if (isset($data['mission_type'])) {
            $type = MissionType::where('name', $data['mission_type'])->first();

            if ($type == null)
                return 'mission_type_not_found';
            else
                $mission->type_id = $type->id;
        } else
            $mission->type_id = 1;

        return $mission;
    }
}
