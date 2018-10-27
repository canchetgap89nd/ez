<?php

namespace App\models\Admin\PackageAndPayment;

use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    protected $fillable = [
    	'user_id', 'amount', 'tax', 'other_payment', 'discount', 'total_payable', 'total_after_discount', 'paid', 'pay_code', 'admin_id', 'package_id', 'duration', 'price', 'duration_bonus', 'is_active'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function package()
    {
    	return $this->belongsTo('App\models\Admin\PackageAndPayment\Package', 'package_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\models\UserAdmin', 'admin_id');
    }
}
