<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Transaction extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'status','user_id','package_id',
    ];


    protected $primaryKey = 'id';

    public function User(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package(){
        return $this->belongsTo(UserPackage::class, 'package_id');
    }
}