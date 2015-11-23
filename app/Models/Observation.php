<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model{

    protected $table = 'observations';

    protected $fillable = ['device_uuid', 'latitude', 'longitude', 'observation_date'];


    public function measurements(){
        return $this->hasMany('App\Models\Measurement', 'observation_id', 'observation_id');
    }

}
