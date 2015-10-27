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
     * Register a new user to the app
     *
     * @return mixed
     */
    public function register() {

        $credentials = \Request::only('email', 'password', 'name');

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
                $response->success = false;
                $response->errors = [
                    'id' => '',
                    'message' => 'invalid_credentials'];

                return \Response::json($response);
            }
        } catch (JWTException $e) {
            // something went wrong
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'message' => 'could_not_create_token'];

            return \Response::json($response);
        }

        // if no errors are encountered we can return a JWT
        $response = new ApiResponse();
        $response->success = true;
        $response->data = ['token'=> $token];

        return \Response::json($response);
    }

}
