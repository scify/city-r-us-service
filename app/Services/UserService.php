<?php namespace App\Services;


use App\Models\ApiResponse;
use App\Models\User;

class UserService {


    /**
     * The register method is responsible for checking/validating
     * the given input (email and password) and either return an error code or
     * create a new user.
     *
     * @param $credentials
     * @return mixed
     */
    public function register($credentials) {

        //Validations
        if ($credentials['name'] == null || $credentials['name'] == '') {
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'description' => 'name_is_null',
                'message' => 'The user\'s name should not be null or an empty string.'];

            return \Response::json($response);
        }


        if ($credentials['email'] == null || $credentials['email'] == '') {
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'description' => 'email_is_null',
                'message' => 'The user email should not be null or an empty string.'];

            return \Response::json($response);
        }

        if ($credentials['password'] == null || $credentials['password'] == '') {
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'description' => 'password_is_null',
                'message' => 'The user password should not be null or an empty string.'];

            return \Response::json($response);
        }

        if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'description' => 'email_bad_format',
                'message' => 'The user email should be in a correct email format (i.e. example@example.com).'];

            return \Response::json($response);
        }

        if (strlen($credentials['password']) < 5) {
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'description' => 'password_bad_format',
                'message' => 'The user password should be at least 6 characters long.'];

            return \Response::json($response);
        }

        //Check if email already exists in db
        $user = User::where('email', $credentials['email'])->first();

        if ($user != null) {
            $response = new ApiResponse();
            $response->success = false;
            $response->errors = [
                'id' => '',
                'description' => 'email_exists',
                'message' => 'The email provided is already in use.'];

            return \Response::json($response);
        }

        //All's good, create a new user
        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);

        //Retrieve the JWT and send back to the Controller
        $token = \JWTAuth::fromUser($user);

        $response = new ApiResponse();
        $response->success = true;
        $response->data = [
            'token' => $token,];

        return \Response::json($response);


    }

}
