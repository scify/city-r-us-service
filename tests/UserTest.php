<?php

class UserTest extends TestCase {

    public function testRegistration() {
        $this->post('/users/register', [
            'name' => env('TEST_NAME'),
            'email' => env('TEST_MAIL'),
            'password' => env('TEST_PASS'),
            'device_uuid' => env('TEST_DEVICE'),
            'model' => env('TEST_DEVICE'),
            'manufacturer' => env('TEST_DEVICE'),
        ])->seeJson([
            'status' => 'error',
            'code' => 'email_exists'
        ]);
    }

    public function testAuthentication() {
        $this->post('/users/authenticate', [
            'email' => env('TEST_MAIL'),
            'password' => env('TEST_PASS')
        ])->seeJson([
            'status' => 'success',
            'email' => env('TEST_MAIL')
        ]);
    }

    public function testFindByEmail() {
        $this->get('/users/byEmail?email=' . env('TEST_MAIL'))->seeJson([
            'status' => 'success',
            'email' => env('TEST_MAIL'),
            'name' => env('TEST_NAME')
        ]);
    }

    public function testFindById() {
        $this->get('/users/byId?id=1')->seeJson([
            'status' => 'success'
        ]);
    }

    public function testGetAll() {
        $this->get('/users')->seeJson([
            'status' => 'success',
            'email' => env('TEST_MAIL'),
            'name' => env('TEST_NAME')
        ]);
    }

    public function testGetAllWithScores() {
        $this->get('/users/withScores')->seeJson([
            'status' => 'success',
            'email' => env('TEST_MAIL'),
            'name' => env('TEST_NAME'),
            'observation_points' => []
        ]);
    }
}
