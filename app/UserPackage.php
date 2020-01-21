<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class UserPackage extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;


    protected $fillable = [
        'package_name', 'package_point' , 'package_category', 'package_description', 'code', 'user_id'
    ];


    public function User(){
        return $this->belongsTo(User::class);
    }
    
    public function transaction(){
        return $this->hasMany(Transaction::class,'package_id');
    }

    public function image(){
        return $this->hasOne('App\Image','image_id');
    }

    protected $primaryKey = 'id';
}