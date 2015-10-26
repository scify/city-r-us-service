<?php namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
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
                $response = new ApiResponse([
                    'success' => false,
                    'errors' => [
                        'id' => '',
                        'message' => 'invalid_credentials']
                ]);
                return \Response::json($response);
            }
        } catch (JWTException $e) {
            // something went wrong
            $response = new ApiResponse([
                'success' => false,
                'errors' => [
                    'id' => '',
                    'message' => 'could_not_create_token']
            ]);
            return \Response::json($response);
        }

        // if no errors are encountered we can return a JWT
        $response = new ApiResponse([
            'success' => true,
            'data' => [
                'token' => $token]
        ]);
        return \Response::json($response);
    }
}
