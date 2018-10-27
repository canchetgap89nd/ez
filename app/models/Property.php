<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
    	'prop_name', 'user_id', 'staff_id'
    ];

    public function hasValue()
    {
    	return $this->hasMany('App\models\PropertyValue', 'prop_id');
    }
}
