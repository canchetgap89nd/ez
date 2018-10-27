<?php

namespace App\models\Admin\PackageAndPayment;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
    	'name', 'display_name', 'description', 'price', 'page_limit', 'staff_limit'
    ];

    public $timestamps = true;

    public function features()
    {
    	return $this->belongsToMany('App\models\Admin\PackageAndPayment\Feature', 'package_features', 'package_id', 'feature_id');
    }

    public function hasFeature($name)
    {
    	$feature = $this->features()->where('name', $name)->first();
    	if (empty($feature)) {
    		return false;
    	}
    	return true;
    }
}
