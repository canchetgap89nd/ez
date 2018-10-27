<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    	'order_id', 'customer_id', 'amount_pay', 'refund', 'user_id', 'staff_id'
    ];
}
