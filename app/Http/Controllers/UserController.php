<?php namespace App\Http\Controllers;


use App\Models\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;


class UserController extends Controller {

    private $userService;

    /**
     * When the class is constructed,
     * also initialize the services needed
     * and the middlewares
     */
    public function __construct() {
        //$this->middleware('jwt.auth', ['only' => ['register']]);
        $this->userService = new UserService();
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
     *			name="name",
     *			description="The user's name",
     *      	required=true,
     *      	type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *			name="email",
     *			description="The user's email",
     *      	required=true,
     *      	type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *			name="password",
     *			description="The user's password",
     *      	required=true,
     *      	type="string",
     *          in="query"
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

        $credentials = \Request::only('email', 'password', 'name');

        if(!\Request::has('role'))
            $credentials['role'] = 'mobile';
        else
            $credentials['role'] = 'web';

        $response = $this->userService->register($credentials);

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
        $response->success = false;
        $response->data = [
            'users' => $users];

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
     *			name="email",
     *			description="The user's email",
     *      	required=true,
     *      	type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *			name="password",
     *			description="The user's password",
     *      	required=true,
     *      	type="string",
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
        $response->message = ['token'=> $token];

        return \Response::json($response);
    }

}
