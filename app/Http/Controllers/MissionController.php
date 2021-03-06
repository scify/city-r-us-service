<?php

namespace App\Http\Controllers;

use App\Models\ApiResponse;
use App\Models\Mission;
use App\Models\SuggestedMission;
use App\Models\User;
use App\Services\MissionService;
use App\Services\Radical\RadicalIntegrationManager;
use App\Services\UserService;

class MissionController extends Controller {

    private $missionService;
    private $radicalIntegrationManager;
    private $userService;

    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['store', 'update', 'destroy', 'suggestMission']]);
        $this->missionService = new MissionService();
        $this->radicalIntegrationManager = new RadicalIntegrationManager();
        $this->userService = new UserService();
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
     *     @SWG\Parameter(
     *       name="from",
     *       description="Limit to missions created at the date provided or later. Format: dd-mm-yyyy or mm/dd/yyyy",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="to",
     *       description="Limit to missions created at the date provided or earlier. Format: dd-mm-yyyy or mm/dd/yyyy",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function index() {
        $from = null;
        $to = null;
        if (\Request::has('from')) {
            $from = date('Y-m-d', strtotime(\Request::get('from')));
        }
        if (\Request::has('to')) {
            $to = date('Y-m-d', strtotime(\Request::get('to')));
        }
        $missions = Mission::with('type', 'users');
        if ($from != null) {
            $missions = $missions->where('created_at', '>=', $from);
        }
        if ($to != null) {
            $missions = $missions->where('created_at', '<=', $to);
        }
        $missions = $missions->get();

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'missions' => $missions];

        return \Response::json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get all missions and the observations contributed by users",
     *     path="/missions/observations",
     *     description="Get all missions and the ovservations contributed by users.",
     *     operationId="api.missions.observations",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="from",
     *       description="Limit to observations created at the date provided or later. Format: dd-mm-yyyy or mm/dd/yyyy",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="to",
     *       description="Limit to observations created at the date provided or earlier. Format: dd-mm-yyyy or mm/dd/yyyy",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns all the missions of the application with their observations",
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
    public function withObservations() {
        $from = null;
        $to = null;
        if (\Request::has('from')) {
            $from = date('Y-m-d', strtotime(\Request::get('from')));
        }
        if (\Request::has('to')) {
            $to = date('Y-m-d', strtotime(\Request::get('to')));
        }
        $missions = Mission::with('type', 'devices.observations')->get();
        if ($from != null || $to != null) {
            $from = \Carbon::parse($from == null ? '01-01-1990' : $from);
            $to = \Carbon::parse($to == null ? '01-01-3000' : $to);
            foreach ($missions as $mid => $mission) {
                foreach ($mission->devices as $did => $device) {
                    foreach ($device->observations as $oid => $observation) {
                        if ($from->gt($observation->created_at) || $to->lt($observation->created_at)) {
                            unset($device->observations[$oid]);
                        }
                    }
                    if (count($device->observations) == 0) {
                        unset($mission->devices[$did]);
                    }
                }
                if (count($mission->devices) == 0) {
                    unset($missions[$mid]);
                }
            }
        }
        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'missions' => $missions
        ];
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
     *       required=true,
     *       default="",
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
     * Delete a mission
     *
     * @SWG\Post(
     *     summary="Delete a mission",
     *     path="/missions/delete",
     *     description="Delete the mission from the db and Radical API",
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
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the id of the deleted mission",
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
                    try {
                        $this->radicalIntegrationManager->deleteMission($mission);
                    } catch (\Exception $ex) {
                        //TODO: handle exception
                        // return ($ex);
                    }
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
     * Find a mission by name
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get observations for a given mission",
     *     path="/missions/{id}/observation",
     *     description="Retrieve the location data for a given observation",
     *     operationId="api.missions",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="id",
     *       description="The mission's id",
     *       required=true,
     *       type="integer",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="from",
     *       description="Limit to observations created at the date provided or later. Format: dd-mm-yyyy or mm/dd/yyyy",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="to",
     *       description="Limit to observations created at the date provided or earlier. Format: dd-mm-yyyy or mm/dd/yyyy",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     * @SWG\Response(
     *         response=200,
     *         description="Returns a mission",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/mission")
     *         )
     *     )
     * )
     */
    public function byIdWithObservations($id) {
        $from = null;
        $to = null;
        if (\Request::has('from')) {
            $from = date('Y-m-d', strtotime(\Request::get('from')));
        }
        if (\Request::has('to')) {
            $to = date('Y-m-d', strtotime(\Request::get('to')));
        }
        $mission = Mission::where('id', $id)->with('type', 'devices.observations.measurements')->first();

        if ($mission == null) {
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'mission_not_found',
                'description' => 'The mission could not be found'];
        } else {
            foreach ($mission->devices as $dKey => $device) {
                foreach ($device->observations as $oKey => $observation) {
                    $oDate = explode(" ", $observation->observation_date)[0];
                    if ($from != null && strcmp($from, $oDate) > 0) {
                        unset($device->observations[$oKey]);
                        continue;
                    } else if ($to != null && strcmp($to, $oDate) < 0) {
                        unset($device->observations[$oKey]);
                        continue;
                    }
                    $tmp = explode(".", $observation->device_uuid);

                    if ($tmp[1] != $mission->radical_service_id || !isset($observation->measurements) || sizeof($observation->measurements) == 0) {
                        unset($device->observations[$oKey]);
                    }
                }
                if (sizeof($device->observations) == 0) {
                    unset($mission->devices[$dKey]);
                }
            }

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
     * Award a user for a certain mission
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Award a user for a certain mission",
     *     path="/missions/awardUser",
     *     description="Give a user an award for their contribution to a particular mission",
     *     operationId="api.missions.awardUser",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="missionId",
     *       description="The mission's id",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="userId",
     *       description="The users's id",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="award",
     *       description="The award",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function awardUser() {
        $response = new ApiResponse();
        $response->status = 'error';
        $response->message = [
            'id' => '',
            'code' => 'not_implemented',
            'description' => 'The action is not implemented yet'];

        return \Response::json($response);
    }

    /**
     * Get the top contributors for a certain mission
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get the top contributors for a certain mission",
     *     path="/missions/topContributors",
     *     description="Get the top contributors",
     *     operationId="api.missions.topContributors",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="missionId",
     *       description="The mission's id",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function topContributors() {

        $mission = Mission::with('devices.observations', 'devices.user')->find(\Request::get('missionId'));

        $users = [];

        if ($mission != null) {

            foreach ($mission->devices as $device) {
                $device->user->totalContribution = sizeof($device->observations);
                array_push($users, $device->user);
            }


            $response = new ApiResponse();
            $response->status = 'success';
            $response->message = [
                'users' => $users
            ];

            return \Response::json($response);
        } else {

            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [];
            return \Response::json($response);
        }
    }

    /**
     * Suggest a mission
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Suggest a mission",
     *     path="/missions/suggest",
     *     description="Users of the application are able to suggest a mission",
     *     operationId="api.missions.suggest",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header, in order to authenticate the user making the call. Format should be: Authorization: Bearer x.y.z",
     *       required=true,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *     @SWG\Parameter(
     *       name="description",
     *       description="The description of the suggested mission",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function suggestMission() {

        SuggestedMission::create([
            'description' => \Request::get('description'),
            'user_id' => \Auth::user()->id
        ]);

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = "Mission suggested";


        $admins = $this->userService->admins();
        $user = User::find(\Auth::user()->id);

        //send an email to all the admins
        foreach ($admins as $admin) {
            $email = $admin->email;
            \Mail::send('emails.new_suggested_mission', ['admin' => $admin, 'user' => $user, 'missionDescription' => \Request::get('description')], function ($message) use ($email) {
                $message->to($email)->subject('Νέα προτεινόμενη αποστολή στο City-R-US!');
            });
        }

        return \Response::json($response);
    }

    /**
     * Suggest a mission from website
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Suggest a mission",
     *     path="/missions/suggest",
     *     description="Users of the website are able to suggest a mission",
     *     operationId="api.missions.suggest",
     *     produces={"application/json"},
     *     tags={"missions"},
     *     @SWG\Parameter(
     *       name="description",
     *       description="The description of the suggested mission",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="mail",
     *       description="The e-mail of the person suggesting a mission",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="name",
     *       description="The name of the person suggesting a mission",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     )
     * )
     */
    public function suggestMissionWeb() {

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = "Mission suggestion sent";


        $admins = $this->userService->admins();

        //send an email to all the admins
        foreach ($admins as $admin) {
            $email = $admin->email;
            \Mail::send('emails.new_suggested_mission_web', ['admin' => $admin, 'name' => \Request::get('name'), 'mail' => \Request::get('mail'), 'missionDescription' => \Request::get('description')], function ($message) use ($email) {
                $message->to($email)->subject('Νέα προτεινόμενη αποστολή στο City-R-US!');
            });
        }

        return \Response::json($response);
    }

}
