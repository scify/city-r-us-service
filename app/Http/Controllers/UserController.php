<?php namespace App\Http\Controllers;


use App\Models\ApiResponse;
use App\Models\User;
use App\Services\DeviceService;
use App\Services\UserService;
use Illuminate\Http\Request;


class UserController extends Controller {

    private $userService;
    private $deviceService;

    /**
     * When the class is constructed,
     * also initialize the services needed
     * and the middlewares
     */
    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['byJWT']]);
        $this->userService = new UserService();
        $this->deviceService = new DeviceService();
    }


    /**
     * Register a new user to the app.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Register a user",
     *     path="/users/register",
     *     description="Register a new user to the app.",
     *     operationId="api.users.register",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *            name="name",
     *            description="The user's name",
     *        required=true,
     *        type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *            name="email",
     *            description="The user's email",
     *        required=true,
     *        type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *            name="password",
     *            description="The user's password",
     *        required=true,
     *        type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="device_name",
     *       description="The name of the device",
     *       required=true,
     *       default="",
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="model",
     *       description="The model of the device",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="manufacturer",
     *       description="The manufacturer of the device",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User registered",
     *         @SWG\Schema(ref="#/definitions/apiResponse")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Malformed data",
     *     ),
     *     @SWG\Response(
     *         response=409,
     *         description="Email already exists",
     *     )
     * )
     */
    public function register() {

        $response = $this->userService->register();

        return $response;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get all users",
     *     path="/users",
     *     description="Retrieve the users of the application.",
     *     operationId="api.users",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns all the users of the application",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/user")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function index() {

        $users = User::all();

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'users' => $users];

        return \Response::json($response);
    }

    /**
     * Display one user by id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get user by email",
     *     path="/users/byEmail",
     *     description="Retrieve the user that corresponds to a certain email",
     *     operationId="api.users.byEmail",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *        name="email",
     *        description="The user's email",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the user based on a certain id",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/user")
     *         ),
     *     )
     * )
     */
    public function byEmail() {

        $response = new ApiResponse();

        if (!\Request::has('email')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'email_null',
                'description' => 'The user email should not be null'];
        } else {
            $user = User::where('email', \Request::get('email'))->with('points')->first();

            if ($user == null) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'user_not_found',
                    'description' => 'The user could not be found'];
            } else {
                $response->status = 'success';
                $response->message = [
                    'user' => $user];
            }
        }
        return \Response::json($response);
    }

    /**
     * Display one user by JWT
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get user by JWT",
     *     path="/users/byJWT",
     *     description="Retrieve the user that corresponds to a certain JWT",
     *     operationId="api.users.byJWT",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header, in order to authenticate the user making the call. Format should be: Authorization: Bearer x.y.z",
     *       required=true,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns the user based on a certain JWT",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/user")
     *         ),
     *     )
     * )
     */
    public function byJWT() {

        $user = User::find(\Auth::user()->id);

        return $user;

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = ['user' => $user];

        return \Response::json($response);
    }

    /**
     * Authenticate a user based on given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Authenticate a user",
     *     path="/users/authenticate",
     *     description="Authenticate a user based on given credentials.",
     *     operationId="api.users.authenticate",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *            name="email",
     *            description="The user's email",
     *        required=true,
     *        type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *            name="password",
     *            description="The user's password",
     *        required=true,
     *        type="string",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User authenticated",
     *         @SWG\Schema(ref="#/definitions/apiResponse")
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = \JWTAuth::attempt($credentials)) {
                $response = new ApiResponse();
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'invalid_credentials',
                    'description' => 'The credentials provided are not valid'];

                return \Response::json($response);
            }
        } catch (JWTException $e) {
            // something went wrong
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'could_not_create_token',
                'description' => 'The token could not be created'];

            return \Response::json($response);
        }

        // if no errors are encountered we can return a JWT
        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = ['token' => $token];

        return \Response::json($response);
    }

}
