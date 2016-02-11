<?php

class ObservationTest extends TestCase {

    public function testGetAll() {
        $this->get('/observations')->seeJson([
            'status' => 'success'
        ]);
    }

    public function testStore() {
        $this->login();
        $this->post('/observations/store', [
            'device_uuid' => -1,
            'mission_id' => 1,
            'latitude' => 0,
            'longitude' => 0,
            'observation_date' => \Carbon\Carbon::now(),
            'measurements' => []
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ])->seeJson([
            'status' => 'error',
            'code' => 'device_not_found'
        ]);
    }

    public function testGetByMissionId() {
        $this->get('/getObservationsByMissionId/1')->seeJson([
            'status' => 'success'
        ]);
    }

    public function testGetByMissionIdWithDate() {
        $this->get('/getObservationsByMissionId/1/15-3-2016')->seeJson([
            'status' => 'success'
        ]);
    }

}
