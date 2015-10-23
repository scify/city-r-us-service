<?php namespace App\Http\Controllers;


use App\Models\User;
use App\Services\UserService;

class UserController extends Controller {

    private $userService;

    /**
     * When the class is constructed,
     * also initialize the services needed
     * and the middlewares
     */
    public function __construct() {
        $this->middleware('jwt.auth', ['except' => ['register']]);
        $this->userService = new UserService();
    }

    /**
     * Register a new user to the app
     *
     * @return mixed
     */
    public function register() {

        $credentials = \Request::only('email', 'password');

        $response = $this->userService->register($credentials);

        return $response;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/users",
     *     description="Retrieve the users of the application.",
     *     operationId="api.users",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *			name="jwtToken",
     *			description="The JWT is required in order to authenticate the user",
     *      	required=true,
     *      	type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns all the users of the application."
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function index() {

        $users = User::all();

        return \Response::json([
            'success' => true,
            'data' => [
                'users' => $users]
        ]);
    }

}
