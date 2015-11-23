<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model{

    protected $table = 'devices';

    protected $fillable = ['device_uuid', 'model', 'manufacturer', 'latitute', 'longitude', 'type', 'status', 'registration_date'];


    public function capabilities(){
        return $this->hasMany('App\Models\DeviceCapability', 'device_uuid', 'device_uuid');
    }

}
