<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class VietNamProvince extends Model
{
    protected $table = 'viet_nam_provinces';

    public function districts()
    {
    	return $this->hasMany('App\models\VietNamDistrict', 'province_id');
    }
}
