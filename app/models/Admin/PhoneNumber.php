<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    protected $fillable = [
    	'phone', 'uid'
    ];
}
