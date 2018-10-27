<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class DeviceInfo extends Model
{
    protected $fillable = ['user_id', 'token'];

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }
}
