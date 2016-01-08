<?php

class RouteStrategy implements BasicStrategy {

    private $points = 20;

    public function reward($user, $missionId) {

        $user->points()->save(new Point([
            'mission_id' => $missionId,
            'points' => $this->points
        ]));

        return;

    }
}
