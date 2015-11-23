<?php namespace App\Models;

class Measurements{

    protected $table = 'measurements';

    protected $fillable = ['type', 'value', 'unit', 'latitude', 'longitude', 'observation_date', 'observation_id'];

}
