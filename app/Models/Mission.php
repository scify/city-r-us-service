<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model{

    protected $table = 'missions';

    protected $fillable = ['name', 'description'];


    public function type(){
        return $this->hasOne('App\Models\Descriptions\MissionType', 'id', 'type_id');
    }
}
