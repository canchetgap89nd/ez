<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class VietNamDistrict extends Model
{
    public function wards()
    {
    	return $this->hasMany('App\models\VietNamWard', 'district_id');
    }
}
