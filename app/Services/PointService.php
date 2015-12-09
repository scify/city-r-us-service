<?php namespace App\Services;


use App\Models\Mission;
use App\Models\Point;
use App\Models\User;

class PointService
{

    private $locationPoints = 10;
    private $routePoints = 20;

    /**
     * Reward a user with points based on the type of mission
     * they contributed to.
     */
    public function reward()
    {
        $mission = Mission::with('type')->find(\Request::get('mission_id'));
        $user = User::find(\Auth::user()->id);

        if ($mission->type->name == 'location')
            $points = $this->locationPoints;
        else
           $points = $this->routePoints;

        $user->points()->save(new Point([
            'mission_id' => $mission->id,
            'points' => $points
        ]));

        return $points;
    }

}
