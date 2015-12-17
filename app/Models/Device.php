<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model{

    protected $table = 'devices';

    protected $fillable = ['device_uuid', 'model', 'manufacturer', 'latitude', 'longitude', 'type', 'status', 'registration_date', 'user_id'];


    public function capabilities(){
        return $this->hasMany('App\Models\DeviceCapability', 'device_id', 'id');
    }

    public function observations(){
        return $this->hasMany('App\Models\Observation', 'device_id', 'id');
    }

    public function missions(){
        return $this->belongsToMany('App\Models\Mission', 'devices_missions', 'device_id', 'mission_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
