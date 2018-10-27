<?php

namespace App\models\Admin\PackageAndPayment;

use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    protected $fillable = [
    	'package_id', 'feature_id'
    ];

    public $timestamps = false;
}
