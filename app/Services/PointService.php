<?php namespace App\Services;


use App\Models\Mission;

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
        $mission = Mission::find(\Request::get('id'))->with('type');
        $user = User::find(\Auth::user()->id);

        if ($mission->type->name == 'location')
            $user->points()->attach($mission->id, [
                'points' => $this->locationPoints
            ]);
        else
            $user->points()->attach($mission->id, [
                'points' => $this->routePoints
            ]);

        return;
    }

}