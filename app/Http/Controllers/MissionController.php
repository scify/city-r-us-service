<?php namespace App\Http\Controllers;


use App\Models\ApiResponse;
use App\Models\Descriptions\MissionType;
use App\Models\Mission;
use App\Services\MissionService;

class MissionController extends Controller {

    private $missionService;

    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['store', 'update', 'destroy']]);
        $this->missionService = new MissionService();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get all missions",
     *     path="/missions",
     *     description="Retrieve the missions of the application.",
     *     operationId="api.missions",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns all the missions of the application",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/mission")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
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
    public function update() {
        $mission = Mission::find(\Request::get('id'));

        if ($mission != null) {
            $mission->update(\Request::all());
            $type_id = MissionType::where('name', \Request::get('mission_type'))->first()->id;
            $mission->type_id = $type_id;
            $mission->save();
        }

        return $mission->id;
    }


    /**
     * Find a mission by name
     *
     * @return mixed
     */
    public function byName() {
        $mission = Mission::where('name', \Request::get('name'))->with('type', 'users')->first();

        if ($mission == null) {
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_not_found',
                'description' => 'The mission could not be found'];
        } else {
            $response = new ApiResponse();
            $response->status = 'success';
            $response->message = $mission;
        }
        return \Response::json($response);
    }

    /**
     * Find a mission by id
     *
     * @return mixed
     */
    public function byId() {
        $mission = Mission::where('id', \Request::get('id'))->with('type', 'users')->first();

        if ($mission == null) {
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_not_found',
                'description' => 'The mission could not be found'];
        } else {
            $response = new ApiResponse();
            $response->status = 'success';
            $response->message = $mission;
        }
        return \Response::json($response);
    }

    /**
     * Delete a mission
     */
    public function destroy($id = null) {
        $response = new ApiResponse();
        if ($id == null)
            $id = \Request::get('id');

        if ($id != null) {
            $mission = Mission::with('users')->find($id);
            if ($mission != null) {
                if (sizeof($mission->users) > 0) {
                    $response->status = 'error';
                    $response->message = [
                        'id' => '',
                        'code' => 'mission_has_users',
                        'description' => 'The mission could not be deleted because it has users'];
                } else {
                    //safely delete the mission
                    $mission->delete();
                    $response->status = 'success';
                    $response->message = $id;
                }
            } else {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'mission_not_found',
                    'description' => 'The mission could not be found'];
            }
        } else {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'id_not_provided',
                'description' => 'No id was provided'];
        }

        return \Response::json($response);
    }
}
