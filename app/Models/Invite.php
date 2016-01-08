<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model{


    protected $table = 'invites';

    protected $fillable = ['description', 'user_id', 'token', 'clicked', 'email'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
