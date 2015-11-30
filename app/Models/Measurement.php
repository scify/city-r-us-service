<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *  definition="measurement",
 *   @SWG\Property(property="type"),
 *   @SWG\Property(property="value"),
 *   @SWG\Property(property="unit"),
 *   @SWG\Property(property="latitude"),
 *   @SWG\Property(property="longitude"),
 *   @SWG\Property(property="observation_date"),
 *   @SWG\Property(property="observation_id")
 * )
 */
class Measurement  extends Model{

    protected $table = 'measurements';

    protected $fillable = ['type', 'value', 'unit', 'latitude', 'longitude', 'observation_date', 'observation_id'];


    public function observation()
    {
        return $this->belongsTo('App\Models\Observation', 'observation_id');
    }


}
