<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitePoint extends Model{

    protected $table = 'user_invite_points';

    protected $fillable = ['user_id', 'invite_id', 'points'];

}
