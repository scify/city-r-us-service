<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservationPoint extends Model{

    protected $table = 'user_observations_points';

    protected $fillable = ['user_id', 'mission_id', 'points'];

}
