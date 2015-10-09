<?php namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthenticateController extends Controller {

    public function __construct() {
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        $this->middleware('cors');
    }


    public function index() {
        $users = User::all();
        return $users;
    }

    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = \JWTAuth::attempt($credentials)) {
                return \Response::json([
                    'error' => 'invalid_credentials'
                    ], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return \Response::json([
                    'error' => 'could_not_create_token'
                ], 500);
        }

        // if no errors are encountered we can return a JWT
        return \Response::json([
                'token' => $token
            ], 200);
    }
}
