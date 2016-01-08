<?php

use \App\Models\InvitePoint;

class InviteStrategy implements BasicStrategy {

    private $points = 5;

    public function reward($userId, $inviteId) {

        InvitePoint::create([
            'user_id' => $userId,
            'invite_id' => $inviteId,
            'points' => $this->points
        ]);

        return;
    }
}
