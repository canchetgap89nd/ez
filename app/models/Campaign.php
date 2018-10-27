<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
	protected $fillable = [
		'camp_name', 'user_id', 'perc_disc', 'mon_disc', 'start_time', 'end_time', 'sold_out', 'count_sell', 'revenue', 'spent_money', 'status'
	];

    public function products()
    {
    	return $this->belongsToMany('App\models\Product', 'campaign_products', 'camp_id', 'prod_id');
    }
}
