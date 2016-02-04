<?php namespace App\Http\Controllers;


use App\Models\ApiResponse;
use App\Models\User;
use App\Services\DeviceService;
use App\Services\UserService;
use Illuminate\Http\Request;


class UserController extends Controller
{

    private $userService;
    private $deviceService;

    /**
     * When the class is constructed,
     * also initialize the services needed
     * and the middlewares
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['byJWT', 'invite', 'changePassword']]);
        // $this->middleware('jwt.refresh', ['only' => ['byJWT']]);

        $this->userService = new UserService();
        $this->deviceService = new DeviceService();
    }


    /**
     * Register a new user to the mobile application
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Register a user",
     *     path="/users/register",
     *     description="Register a new user to the mobile application.",
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
     *       name="device_uuid",
     *       description="The uuid of the device",
     *       required=false,
     *       default="",
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="model",
     *       description="The model of the device",
     *       required=false,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="manufacturer",
     *       description="The manufacturer of the device",
     *       required=false,
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
    public function register()
    {

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
    public function index()
    {

        $users = User::all();

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = [
            'users' => $users];

        return \Response::json($response);
    }


    /**
     * Display a listing of the resource including user observation points.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get all users with their observation points",
     *     path="/users/withScores",
     *     description="Retrieve the users of the application with their respective observation points.",
     *     operationId="api.users.withScores",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Response(
     *         response=200,
     *         description="Retrieve the users of the application with their respective observation points.",
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
    public function scores()
    {

        $users = User::with('observationPoints')->get();

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
    public function byEmail()
    {

        $response = new ApiResponse();

        if (!\Request::has('email')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'email_null',
                'description' => 'The user email should not be null'];
        } else {
            $user = User::where('email', \Request::get('email'))->with('observationPoints', 'invitePoints')->first();

            if ($user == null) {
                $response->status = 'error';
                $response->message = [
                    'id' => '',
                    'code' => 'user_not_found',
                    'description' => 'The user could not be found'];
            } else {
                $user = $this->userService->totalPoints($user);

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
    public function byJWT()
    {
        $user = User::with('observationPoints', 'invitePoints')->find(\Auth::user()->id);

        $user = $this->userService->totalPoints($user);

        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = ['user' => $user];

        return \Response::json($response);
    }


    /**
     * Display one user by id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     summary="Get user by id",
     *     path="/users/byId",
     *     description="Retrieve the user that corresponds to a certain id",
     *     operationId="api.users.byId",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *        name="id",
     *        description="The user's id",
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
    public function byId()
    {

        $response = new ApiResponse();

        if (!\Request::has('id')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'id_null',
                'description' => 'The user id should not be null'];
        } else {
            $user = User::find(\Request::get('id'));

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
    public function authenticate(Request $request)
    {
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

        $user = User::where('email', $credentials['email'])->first();

        // if no errors are encountered we can return a JWT
        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = ['token' => $token, 'user' => $user];

        return \Response::json($response);
    }

    /**
     * Refresh a user JWT token
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Refresh JWT token",
     *     path="/users/refreshToken",
     *     description="Refresh a user token",
     *     operationId="api.users.refreshToken",
     *     produces={"application/json"},
     *     tags={"users"},
     *    @SWG\Parameter(
     *       name="Authorization",
     *       description="The JWT must be present in the Authorization header. Format should be: Authorization: Bearer x.y.z",
     *       required=false,
     *       type="string",
     *       in="header",
     *       schema="json"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Valid response",
     *         @SWG\Schema(ref="#/definitions/apiResponse")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Unauthorized action",
     *     )
     * )
     */
    public function refreshToken(Request $request)
    {
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


    /**
     * Reset a user password.
     * Get a user email, generate a random password and email it to the user
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Reset a user's password",
     *     path="/users/resetPassword",
     *     description="Reset a user password",
     *     operationId="api.users.resetPassword",
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
     *         description=""
     *     )
     * )
     */
    public function resetPassword()
    {

        $user = User::where('email', \Request::get('email'))->first();

        $response = new ApiResponse();

        if ($user == null) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'user_not_found',
                'description' => 'Email was not found'];
        } else {
            $password = str_random(8);
            $user->password = bcrypt($password);
            $user->save();

            //send the user an email containing the new password
            \Mail::send('emails.password_reset', ['user' => $user, 'password' => $password], function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject('[City-R-US] Επαναφορά κωδικού πρόσβασης');
            });

            $response->status = 'success';
            $response->message = [
                'message' => 'An email will be sent containing the new password.'];
        }

        return \Response::json($response);
    }

    /**
     * Change a user password.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Change a user's password",
     *     path="/users/changePassword",
     *     description="Change a user password",
     *     operationId="api.users.changePassword",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *        name="password",
     *        description="The new user password",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *  @SWG\Parameter(
     *        name="passwordConfirmation",
     *        description="Password confirmation",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description=""
     *     )
     * )
     */
    public function changePassword()
    {

        $user = User::find(\Auth::user()->id);
        $user->update(['password' => bcrypt(\Request::get('password'))]);

        $response = new ApiResponse();

        $response->status = 'success';
        $response->message = [
            'message' => 'Password changed'];

        return \Response::json($response);
    }
}
