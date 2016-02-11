<?php

use App\Models\User;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    protected $baseUrl = 'http://localhost/api/v1';
    
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    protected function login() {
        if (isset($this->token)) {
            return;
        }
        $user = User::where('email', '=', env('TEST_MAIL'))->get()[0];
        $this->token = JWTAuth::fromUser($user);
    }

}
