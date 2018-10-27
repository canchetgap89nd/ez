<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ProductProperty extends Model
{
   	public function propertyValue()
   	{
   		return $this->belongsTo('App\models\PropertyValue', 'prop_value_id');
   	}
}
