<?php namespace App\Http\Controllers;


use App\Models\ApiResponse;
use App\Models\Descriptions\MissionType;
use App\Models\Mission;
use App\Services\MissionService;

class MissionController extends Controller {

    private $missionService;

    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['store',  'update', 'destroy']]);
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
     * Store a mission.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Store a new mission",
     *     path="/missions/store",
     *     description="Create and save a new mission.",
     *     operationId="api.missions",
     *     produces={"application/json"},
     *     tags={"missions"},
     *      @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header, in order to authenticate the user making the call. Format should be: Authorization: Bearer x.y.z",
     *       required=true,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *     @SWG\Parameter(
     *        name="name",
     *        description="The missions's name",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="description",
     *       description="The mission's description",
     *       required=false,
     *       default=" ",
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="mission_type",
     *       description="The mission's type. Should be either 'location' or 'route'",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="img_name",
     *       description="The mission's image name. Images are saved to the web app.",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the id of the mission created",
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
    public function store() {
        return $this->missionService->store(\Request::all());
    }


    /**
     * Update a mission.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Update an existing mission",
     *     path="/missions/update",
     *     description="Update the data of an existing mission",
     *     operationId="api.missions",
     *     produces={"application/json"},
     *     tags={"missions"},
     *      @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header, in order to authenticate the user making the call. Format should be: Authorization: Bearer x.y.z",
     *       required=true,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *      @SWG\Parameter(
     *        name="id",
     *        description="The missions's id",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *        name="name",
     *        description="The missions's name",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="description",
     *       description="The mission's description",
     *       required=false,
     *       default=" ",
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="mission_type",
     *       description="The mission's type. Should be either 'location' or 'route'",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="img_name",
     *       description="The mission's image name. Images are saved to the web app.",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the id of the updated mission",
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
    public function update() {
        return $this->missionService->update(\Request::all());
    }


    /**
     * Find a mission by name
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get a mission by name",
     *     path="/missions/byName",
     *     description="Retrieve a mission based on a given name",
     *     operationId="api.missions",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="name",
     *       description="The mission's name",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a mission",
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
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get a mission by id",
     *     path="/missions/byId",
     *     description="Retrieve a mission based on a given id",
     *     operationId="api.missions",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="id",
     *       description="The mission's id",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a mission",
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
    public function destroy($id) {
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
