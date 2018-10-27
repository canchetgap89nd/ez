<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
    	'note', 'amount', 'order_id', 'user_id', 'staff_id'
    ];
}
