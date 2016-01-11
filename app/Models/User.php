<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *   definition="user",
 *   @SWG\Property(property="id"),
 *   @SWG\Property(property="name"),
 *   @SWG\Property(property="email"),
 *   @SWG\Property(property="created_at"),
 *   @SWG\Property(property="updated_at")
 * )
 *
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function roles() {
        return $this->belongsToMany('App\Models\Descriptions\Role', 'users_roles');
    }

    public function devices() {
        return $this->hasMany('App\Models\Device', 'user_id', 'id');
    }

    public function missions() {
        return $this->belongsToMany('App\Models\Mission', 'users_missions', 'user_id', 'mission_id');
    }

    public function observationPoints() {
        return $this->hasMany('App\Models\ObservationPoint', 'user_id', 'id');
    }

    public function invitePoints() {
        return $this->hasMany('App\Models\InvitePoint', 'user_id', 'id');
    }

    public function scopeMissionPoints($query, $missionId) {
        return $query->whereHas('points', function ($q) use ($missionId) {
            $q->where('id', $missionId);
        });
    }

}
