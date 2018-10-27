<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    protected $fillable = [
    	'prod_id', 'prod_code', 'prod_thumb', 'prod_name', 'properties', 'order_id', 'quantity', 'price', 'price_sale', 'camp_id'
    ];
}
