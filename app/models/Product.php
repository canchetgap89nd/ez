<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Image;

class Product extends Model
{
    protected $fillable = [
        'prod_code', 'prod_name', 'parent_id', 'user_id', 'staff_id', 'prod_price', 'prod_price_imp', 'prod_quantity', 'prod_thumb', 'count_childs', 'properties', 'count_sold', 'status'
    ];
    public function properties()
    {
    	return $this->belongsToMany('App\models\PropertyValue', 'product_properties', 'prod_id', 'prop_value_id');
    }

    public function images()
    {
        return $this->hasMany('App\models\Image', 'target_id')->where('type', 'PRODUCT');
    }

    public function cates()
    {
    	return $this->belongsToMany('App\models\Category', 'cate_products', 'product_id', 'cate_id');
    }

    public function childs()
    {
        return $this->hasMany('App\models\Product', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\models\Product', 'parent_id');
    }

    public function orders()
    {
        return $this->belongsToMany('App\models\Order', 'product_orders', 'prod_id', 'order_id')->withPivot('quantity', 'price', 'price_sale', 'camp_id');
    }

    public function imports()
    {
        return $this->belongsToMany('App\models\ImportProduct', 'products_imports', 'prod_id', 'import_id');
    }

    public function exports()
    {
        return $this->belongsToMany('App\models\ExportProduct', 'product_exports', 'prod_id', 'export_id');
    }

}
