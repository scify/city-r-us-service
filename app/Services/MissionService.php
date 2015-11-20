<?php namespace App\Services;


use App\Models\ApiResponse;
use App\Models\Descriptions\MissionType;
use App\Models\Mission;
use App\Services\Radical\RadicalConfigurationAPI;

class MissionService{

    private $radicalServiceConfiguration;
    function __construct()
    {
        $this->radicalServiceConfiguration  = new RadicalConfigurationAPI();
    }

    public function store($data){

        $response = new ApiResponse();
        $status = 200;

        //Validations
        if ($data['name'] == null || $data['name'] == '') {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The mission\'s name should not be null or an empty string.'];

            $status = 400;
        } else {

            $mission = $this->sanitize($data);
            $mission->save();
          //  $this->$radicalServiceConfiguration->registerMission($mission);

            $response->status = 'success';
            $response->message = $mission->id;
        }
        return \Response::json($response, $status);
    }


    public function update($data){

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
            $mission->save();

           // $this->radicalServiceConfiguration->updateMission($mission);

            $response->status = 'success';
            $response->message = $mission->id;
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
