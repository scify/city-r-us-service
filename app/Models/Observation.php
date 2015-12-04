<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *  definition="observation",
 *   @SWG\Property(property="device_id"),
 *   @SWG\Property(property="latitude"),
 *   @SWG\Property(property="longitude"),
 *   @SWG\Property(property="observation_date")
 * )
 *
 */
class Observation extends Model{

    protected $table = 'observations';

    protected $fillable = ['device_id', 'latitude', 'longitude', 'observation_date'];


    public function measurements(){
        return $this->hasMany('App\Models\Measurement', 'observation_id', 'id');
    }

}
