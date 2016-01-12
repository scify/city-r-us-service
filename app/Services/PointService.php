<?php namespace App\Services;


use App\Models\InvitePoint;
use App\Models\Mission;
use App\Models\ObservationPoint;
use App\Models\Point;

class PointService {

    private $invitePoints = 5;
    private $locationPoints = 10;
    private $routePoints = 20;


    public function observationReward() {
        $mission = Mission::with('type')->find(\Request::get('mission_id'));

        if ($mission->type->name == 'location')
           return $this->locationReward(\Auth::user()->id, $mission->id);
        else
            return $this->routeReward(\Auth::user()->id, $mission->id);
    }

    public function locationReward($userId, $missionId) {

        ObservationPoint::create([
            'user_id' => $userId,
            'mission_id' => $missionId,
            'points' => $this->locationPoints
        ]);

        return $this->locationPoints;
    }

    public function routeReward($userId, $missionId) {

        ObservationPoint::create([
            'user_id' => $userId,
            'mission_id' => $missionId,
            'points' => $this->routePoints
        ]);

        return $this->routePoints;
    }

    public function inviteReward($userId, $inviteId) {

        InvitePoint::create([
            'user_id' => $userId,
            'invite_id' => $inviteId,
            'points' => $this->invitePoints
        ]);

        return $this->invitePoints;
    }
}
