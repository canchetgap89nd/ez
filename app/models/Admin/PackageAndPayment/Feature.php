<?php

namespace App\models\Admin\PackageAndPayment;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
    	'name', 'display_name'
    ];
}
