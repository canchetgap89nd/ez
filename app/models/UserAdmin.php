<?php

namespace App\models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAdmin extends Authenticatable
{
    protected $fillable = [
    	'email', 'password', 'avatar'
    ];
}
