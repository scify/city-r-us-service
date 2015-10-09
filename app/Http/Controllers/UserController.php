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
    public function __construct(){
        $this->middleware('jwt.auth', ['except' => ['register']]);
        $this->middleware('cors');
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


}
