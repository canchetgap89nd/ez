<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

	protected $fillable = [
		'cate_thumb', 'cate_name', 'cate_des', 'parent_id', 'user_id', 'total_prods', 'total_order', 'total_amount', 'staff_id'
	];

    public function products()
    {
    	return $this->belongsToMany('App\models\Product', 'cate_products', 'cate_id', 'product_id');
    }

    public function parent()
    {
    	return $this->belongsTo('App\models\Category', 'parent_id');
    }
}
