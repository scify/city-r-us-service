<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class SuggestedMission extends Model{

    use SoftDeletes;

    protected $table = 'suggested_missions';

    protected $fillable = ['description', 'user_id'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
