<?php namespace App\Services;


use App\Models\ApiResponse;
use App\Models\Descriptions\MissionType;
use App\Models\Mission;

class MissionService{



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
        $type_id = MissionType::where('name', $data['type'])->first()->id;
        $mission = Mission::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'type_id' => $type_id
           /*  'imgPath' => bcrypt($credentials['imgPath']),*/
        ]);

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
           'data' => $mission
        ];

        return \Response::json($response, 200);
    }
}
