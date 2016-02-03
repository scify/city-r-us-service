<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuggestedMission extends Model{

    use SoftDeletes;

    protected $table = 'suggested_missions';

    protected $fillable = ['description', 'user_id'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
