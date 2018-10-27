<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PropertyValue extends Model
{
    protected $fillable = [
        'prop_id', 'value', 'user_id', 'staff_id'
    ];

    public function property()
    {
    	return $this->belongsTo('App\models\Property', 'prop_id');
    }

    public function products()
    {
    	return $this->belongsToMany('App\models\Product', 'product_properties', 'prop_value_id', 'prod_id');
    }

    public function ofUser()
    {
    	$propOfVal = $this->property()->first();
    	if ($propOfVal) {
    		return $propOfVal->user_id;
    	}
    	return null;
    }
}
