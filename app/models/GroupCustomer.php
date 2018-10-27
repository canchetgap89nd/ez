<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class GroupCustomer extends Model
{
    protected $fillable = ['user_id', 'group_name', 'group_color', 'id', 'staff_id'];

    public function customers()
    {
    	return $this->belongsToMany('App\models\Customer', 'customer_and_groups', 'group_id', 'customer_id');
    }
}
