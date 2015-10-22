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
    public function register($credentials) {

        //Validations
        if ($credentials['email'] == null || $credentials['email'] == '')
            return \Response::json([
                'success' => false,
                'errors' => [
                    'id' => '',
                    'description' => 'email_is_null',
                    'message' => 'The user email should not be null or an empty string.']
            ]);

        if ($credentials['password'] == null || $credentials['password'] == '')
            return \Response::json([
                'success' => false,
                'errors' => [
                    'id' => '',
                    'description' => 'password_is_null',
                    'message' => 'The user password should not be null or an empty string.']
            ]);

        if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL))
            return \Response::json([
                'success' => false,
                'errors' => [
                    'id' => '',
                    'description' => 'email_bad_format',
                    'message' => 'The user email should be in a correct email format (i.e. example@example.com).']
            ]);

        if (strlen($credentials['password']) < 5)
            return \Response::json([
                'success' => false,
                'errors' => [
                    'id' => '',
                    'description' => 'password_bad_format',
                    'message' => 'The user password should be at least 6 characters long.']
            ]);

        //Check if email already exists in db
        $user = User::where('email', $credentials['email'])->first();

        if ($user != null)
            return \Response::json([
                'success' => false,
                'errors' => [
                    'id' => '',
                    'description' => 'email_exists',
                    'message' => 'The email provided is already in use.']
            ]);

        //All's good, create a new user
        $user = User::create($credentials);

        //Retrieve the JWT and send back to the Controller
        $token = \JWTAuth::fromUser($user);

        return \Response::json([
            'success' => true,
            'data' => [
                'token' => $token]
        ]);
    }

}
