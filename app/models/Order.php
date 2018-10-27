<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $fillable = [
		'order_code', 'user_id', 'cus_id', 'page_id', 'name_receive', 'phone_receive', 'province_id', 'district_id', 'ward_id', 'ad_receive', 'email_receive', 'discount', 'ship_fee', 'other_fee', 'total_value', 'value_has_sale', 'total_amount', 'note_order', 'total_pay', 'status_order', 'deadline_order', 'time_confirmed', 'time_sending', 'time_sent', 'time_refunding', 'time_refunded', 'time_canceled', 'staff_id', 'fb_page_id'
	];

    protected $hidden = [
        'user_id'
    ];

    public function payments()
    {
    	return $this->hasMany('App\models\Payment', 'order_id');
    }

    public function products()
    {
    	return $this->belongsToMany('App\models\Product', 'product_orders', 'order_id', 'prod_id')->withPivot('quantity', 'price', 'price_sale', 'camp_id');
    }

    public function transports()
    {
    	return $this->hasOne('App\models\Transport', 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\models\Customer', 'cus_id');
    }
}
