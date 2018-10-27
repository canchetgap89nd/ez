<?php

namespace App\models\Admin\PackageAndPayment;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $fillable = [
    	'user_id', 'package_id', 'expire_at'
    ];
}
