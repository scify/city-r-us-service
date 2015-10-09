<?php namespace App\Services;


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
    public function register($credentials){

        //Validations
        if ($credentials['email'] == null || $credentials['email'] == '')
            return \Response::json([
                    'error' => 'email_is_null',
                    'message' => 'The user email should not be null or an empty string.'],
                400);

        if ($credentials['password'] == null || $credentials['password'] == '')
            return \Response::json([
                    'error' => 'password_is_null',
                    'message' => 'The user password should not be null or an empty string.'],
                400);

        if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL))
            return \Response::json([
                    'error' => 'email_bad_format',
                    'message' => 'The user email should be in a correct email format (i.e. example@example.com).'],
                400);

        if (strlen($credentials['password']) < 5)
            return \Response::json([
                    'error' => 'password_bad_format',
                    'message' => 'The user password should be at least 6 characters long.'],
                400);

        //Check if email already exists in db
        $user = User::where('email', $credentials['email'])->first();

        if ($user != null)
            return \Response::json([
                    'error' => 'email_exists',
                    'message' => 'The email provided is already in use.'],
                409);

        //All's good, create a new user
        $user = User::create($credentials);

        //Retrieve the JWT and send back to the Controller
        $token = \JWTAuth::fromUser($user);

        return \Response::json(compact('token'));
    }

}
