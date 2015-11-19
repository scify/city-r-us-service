<?php namespace App\Services;


use App\Models\ApiResponse;
use App\Models\Descriptions\MissionType;
use App\Models\Mission;
use App\Services\Radical;

class MissionService{

    private $radicalServiceConfiguration;
    function __construct()
    {
       $this->$radicalServiceConfiguration  = new RadicalConfigurationAPI();
    }

    public function store($data){
        //Validations
        if ($data['name'] == null || $data['name'] == '') {
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The mission\'s name should not be null or an empty string.'];

            return \Response::json($response, 400);
        }
        //All's good, create a new mission
        $type_id = MissionType::where('name', $data['mission_type'])->first()->id;
        $mission = Mission::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'type_id' => $type_id
        ]);

        $this->$radicalServiceConfiguration->registerMission($mission);
        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
           'data' => $mission
        ];

        return \Response::json($response, 200);
    }

    public function update($mission, $data){

        //Validations
        if ($data['name'] == null || $data['name'] == '') {
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The mission\'s name should not be null or an empty string.'];

            return \Response::json($response, 400);
        }
        //All's good, create a new mission
        $type_id = MissionType::where('name', $data['mission_type'])->first()->id;

        if(isset($data['name']))
            $mission->name = $data['name'];
        if(isset($data['img_name']))
            $mission->img_name = $data['img_name'];
        if(isset($data['description']))
            $mission->description = $data['description'];
        if(isset($data['mission_type']))
            $mission->type_id = $type_id;

        $mission->save();

        $this->$radicalServiceConfiguration->updateMission($mission);

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'data' => $mission
        ];

        return \Response::json($response, 200);
    }
}
