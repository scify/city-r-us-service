<?php namespace App\Services;


use App\Models\ApiResponse;
use App\Models\Descriptions\Role;
use App\Models\Device;
use App\Models\User;

class UserService {

    private $deviceService;

    public function __construct() {
        $this->deviceService = new DeviceService();
    }

    /**
     * The register method is responsible for checking/validating
     * the given input (email and password) and either return an error code or
     * create a new user.
     *
     * @return mixed
     */
    public function register() {

        $validateUser = $this->validateUser();

        if ($validateUser->status == 'error')
            return \Response::json($validateUser);
        else {

            //All's good, create a new user
            $user = User::create([
                'name' => \Request::get('name'),
                'email' => \Request::get('email'),
                'password' => bcrypt(\Request::get('password')),
            ]);

            if (!\Request::has('role'))
                $role['role'] = 'mobile';
            else
                $role['role'] = 'web';

            //assign role to user
            $role = Role::where('name', $role)->first();
            $user->roles()->save($role);


            $device = $this->sanitizeDevice($user->id);
            $this->deviceService->store($user->id, $device);

            //Retrieve the JWT and send back to the Controller
            $token = \JWTAuth::fromUser($user);

            $response = new ApiResponse();
            $response->status = 'success';
            $response->message = [
                'token' => $token
            ];
        }

        return \Response::json($response);
    }


    /**
     * Return the user with their total points set
     *
     * @param $user
     * @return int
     */
    public function totalPoints($user) {
        $totalPoints = 0;

        foreach ($user->observationPoints as $point) {
            $totalPoints += $point->points;
        }

        foreach ($user->invitePoints as $point) {
            $totalPoints += $point->points;
        }
        unset($user->observationPoints);
        unset($user->invitePoints);
        $user->totalPoints = $totalPoints;

        return $user;
    }


    /*
     * Get the admins of the web application
     */
    public function admins() {

        $admins = User::whereHas('roles', function ($q) {
            $q->where('name', 'web');
        })->get();

        return $admins;
    }


    /**
     * Validate a single user
     *
     * @return ApiResponse
     */
    private function validateUser() {

        $response = new ApiResponse();

        //Validations
        if (!\Request::has('name') || \Request::get('name') == '') {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'name_is_null',
                'description' => 'The user\'s name should not be null or an empty string.'];
        }


        if (!\Request::has('email') || \Request::get('email') == '') {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'email_is_null',
                'description' => 'The user email should not be null or an empty string.'];
        }


        if (!\Request::has('password') || \Request::get('password') == '') {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'password_is_null',
                'description' => 'The user password should not be null or an empty string.'];
        }

        if (!filter_var(\Request::get('email'), FILTER_VALIDATE_EMAIL)) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'email_bad_format',
                'description' => 'The user email should be in a correct email format (i.e. example@example.com).'];
        }

        if (strlen(\Request::get('password')) < 5) {
            $response = new ApiResponse();
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'password_bad_format',
                'description' => 'The user password should be at least 6 characters long.'];
        }

        //Check if email already exists in db
        $user = User::where('email', \Request::get('email'))->first();
        if ($user != null) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'email_exists',
                'description' => 'The email provided is already in use.'];
        }

        return $response;
    }


    /**
     * Before saving a device, check if data is ok
     *
     * @return ApiResponse
     */
    public function sanitizeDevice($userId) {
        if (!\Request::has('deviceUUID'))
            $deviceUUID = 'test-' . str_random(10);
        else
            $deviceUUID = \Request::get('deviceUUID');

        if (!\Request::has('model'))
            $model = 'test';
        else
            $model = \Request::get('model');


        if (!\Request::has('manufacturer'))
            $manufacturer = 'test';
        else
            $manufacturer = \Request::get('manufacturer');

        $device = new Device([
            'device_uuid' => $deviceUUID,
            'model' => $model,
            'manufacturer' => $manufacturer,
            'user_id' => $userId,
        ]);

        return $device;
    }

}
