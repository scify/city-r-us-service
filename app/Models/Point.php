<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model{

    protected $table = 'user_mission_points';

    protected $fillable = ['user_id', 'mission_id', 'points'];


}
