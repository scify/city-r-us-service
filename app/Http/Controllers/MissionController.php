<?php namespace App\Http\Controllers;


use App\Http\Requests\MissionRequest;
use App\Models\ApiResponse;
use App\Models\Mission;
use App\Services\MissionService;

class MissionController extends Controller {

    private $missionService;

    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['store']]);
        $this->missionService = new MissionService();
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index() {
        $missions = Mission::with('type')->get();

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'missions' => $missions];

        return \Response::json($response);
    }

    /**
     * Store a mission
     *
     * @return mixed
     */
    public function store() {
        return $this->missionService->store(\Request::all());
    }

    /**
     * Update a mission
     *
     * @return mixed
     */
    public function update(){
        dd('aa');
        $mission = Mission::find(\Request::get('id'));
        if($mission!=null)
            $mission->update(\Request::all());

        return \Request::get('id');
    }


    public function byName(){
        $name = \Request::get('name');
        return Mission::where('name', $name)->first();
    }
}
