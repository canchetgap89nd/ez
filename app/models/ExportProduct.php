<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ExportProduct extends Model
{
    protected $fillable = [
    	'export_id', 'prod_id', 'prod_code', 'prod_thumb', 'prod_name', 'properties', 'quantity_ex', 'price_ex', 'inventory_ex'
    ];
}
