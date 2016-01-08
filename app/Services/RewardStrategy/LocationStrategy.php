<?php

class LocationStrategy implements BasicStrategy {

    private $points = 10;

    public function reward($user, $missionId) {

        $user->points()->save(new Point([
            'mission_id' => $missionId,
            'points' => $this->points
        ]));

        return;
    }
}
