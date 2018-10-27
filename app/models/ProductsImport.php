<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ProductsImport extends Model
{
    protected $fillable = [
    	'import_id', 'prod_id', 'prod_code', 'prod_thumb', 'prod_name', 'properties', 'quantity_prod', 'price_imp', 'inventory_prod'
    ];
}
