<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceCapability extends Model{

    protected $table = 'device_capabilities';

    protected $fillable = ['name', 'unit', 'data_type', 'device_uuid'];


}
