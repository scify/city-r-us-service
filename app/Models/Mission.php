<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *  definition="mission",
 *   @SWG\Property(property="id"),
 *   @SWG\Property(property="name"),
 *   @SWG\Property(property="description"),
 *   @SWG\Property(property="img_name"),
 *   @SWG\Property(property="type_id"),
 *   @SWG\Property(property="created_at"),
 *   @SWG\Property(property="updated_at")
 * )
 *
 */
class Mission extends Model{

    protected $table = 'missions';

    protected $fillable = ['name', 'description', 'img_name', 'type_id'];


    public function type(){
        return $this->hasOne('App\Models\Descriptions\MissionType', 'id', 'type_id');
    }

    public function users(){
        return $this->belongsToMany('App\Models\User', 'users_missions', 'mission_id', 'user_id');
    }
}
