<?php

class MissionTest extends TestCase {

    public function testGetAll() {
        $this->get('/missions')->seeJson([
            'status' => 'success'
        ]);
    }

    public function testGetAllWithObservations() {
        $this->get('/missions/observations')->seeJson([
            'status' => 'success'
        ]);
    }

    public function testCreate() {
        $this->login();
        $mission_id = json_decode($this->post('/missions/store', [
            'name' => 'TEST_MISSION',
            'description' => 'TEST_MISSION',
            'mission_type' => 'ROUTE'
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ])->seeJson([
            'status' => 'success'
        ])->response->getContent())->message;
        return $mission_id;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate($mission_id) {
        $this->login();
        $this->post('/missions/update', [
            'id' => $mission_id,
            'name' => 'TEST_MISSION',
            'description' => 'TEST_MISSION_2',
            'mission_type' => 'ROUTE'
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ])->seeJson([
            'status' => 'success',
            'message' => $mission_id
        ]);
        return $mission_id;
    }

    /**
     * @depends testUpdate
     */
    public function testFindById($mission_id) {
        $this->login();
        $this->get('/missions/byId?id=' . $mission_id)->seeJson([
            'status' => 'success',
            'id' => $mission_id,
            'name' => 'TEST_MISSION',
            'description' => 'TEST_MISSION_2'
        ]);
        return $mission_id;
    }

    /**
     * @depends testFindById
     */
    public function testFindByName($mission_id) {
        $this->login();
        $this->get('/missions/byName?name=TEST_MISSION')->seeJson([
            'status' => 'success',
            'id' => $mission_id,
            'name' => 'TEST_MISSION',
            'description' => 'TEST_MISSION_2'
        ]);
        return $mission_id;
    }

    /**
     * @depends testFindByName
     */
    public function testDelete($mission_id) {
        $this->login();
        $this->post('/missions/delete', [
            'id' => $mission_id
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ])->seeJson([
            'status' => 'success',
            'message' => $mission_id
        ]);
    }

    public function testGetContributors() {
        $this->get('/missions/topContributors?missionId=1')->seeJson([
            'status' => 'success'
        ]);
    }

}
