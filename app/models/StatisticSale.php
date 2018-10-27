<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class StatisticSale extends Model
{
    protected $fillable = [
    	'user_id', 'page_id', 'sale_amount', 'back_amount', 'discount', 'revenue', 'origin_val', 'profit', 'action_time', 'ship_fee', 'order_id'
    ];

    protected $hidden = [
    	'user_id'
    ];

    public $timestamps = false;
}
