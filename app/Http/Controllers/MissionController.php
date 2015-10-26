<?php namespace App\Http\Controllers;


use App\Http\Requests\MissionRequest;
use App\Models\ApiResponse;
use App\Models\Mission;

class MissionController extends Controller {


    public function __construct() {
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index() {
        $missions = Mission::all();

        $response = new ApiResponse();
        $response->success = false;
        $response->data = [
            'users' => $missions];

        return \Response::json($response);
    }

    public function store(MissionRequest $request) {

        $mission = Mission::create($request->all());

        return $mission;
    }
}
