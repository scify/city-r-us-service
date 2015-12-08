<?php namespace App\Services;


use App\Models\ApiResponse;
use App\Models\Descriptions\Role;
use App\Services\DeviceService;
use App\Models\User;

class UserService
{

    private $deviceService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
    }

    /**
     * The register method is responsible for checking/validating
     * the given input (email and password) and either return an error code or
     * create a new user.
     *
     * @return mixed
     */
    public function register()
    {

        $validateUser = $this->validateUser();
        $validateDevice = $this->validateDevice();

        if ($validateUser->status == 'error')
            return \Response::json($validateUser);

        else if ($validateDevice->status == 'error')
            return \Response::json($validateDevice);
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

            $this->deviceService->store();


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
     * Validate a single user
     *
     * @return ApiResponse
     */
    private function validateUser()
    {

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
     * Validate the device data before saving to db
     * @return ApiResponse
     */
    public function validateDevice()
    {

        $response = new ApiResponse();

        if (!\Request::has('device_uuid')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'device_uuid_null',
                'description' => 'The device uuid should not be null'];
        }

        if (!\Request::has('model')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'model_null',
                'description' => 'The device model should not be null'];
        }
        if (!\Request::has('manufacturer')) {
            $response->status = 'error';
            $response->message = [
                'id' => '',
                'code' => 'manufacturer_null',
                'description' => 'The device manufacturer should not be null'];
        }

        return $response;
    }

}
