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
        ]) -> seeJson([
            'status' => 'error',
            'code' => 'email_exists'
        ]);
    }

    public function testAuthentication() {
        
    }

    public function testPasswordReset() {
        //TODO: Test
    }

    public function testPasswordChange() {
        //TODO: Test
    }

    public function testFindeByEmail() {
        //TODO: Test
    }

    public function testFindeById() {
        //TODO: Test
    }

    public function testFindeByJWT() {
        //TODO: Test
    }

    public function testGetAll() {
        //TODO: Test
    }

    public function testGetAllWithScores() {
        //TODO: Test
    }

    public function testInvite() {
        //TODO: Test
    }

    public function testInviteAccepted() {
        //TODO: Test
    }

}
