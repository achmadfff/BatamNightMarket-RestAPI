<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'email', 'password', 'code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function user_point_top_up()
    {

        return $this->hasOne(UserPointTopUp::class, 'user_id');
    }

    public function package()
    {
        return $this->hasMany(UserPackage::class, 'user_id');
    }



    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function spend()
    {
        return $this->hasMany(Transaction::class, 'user_id')->where('transactions.status','=','claimed')->join('user_packages', 'package_id', '=', 'user_packages.id')->sum('package_point');
    }

    protected $primaryKey = 'id';

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['sub' => $this->id];
    }
}
