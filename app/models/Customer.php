<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\CustomerReport;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'fb_id_cus', 'name_cus', 'phone_cus', 'birthday_cus', 'gender_cus', 'email_cus', 'address_cus', 'banned', 'fb_page_id', 'staff_id'
    ];

    protected $hidden = [
        'user_id'
    ];

    public function groups()
    {
    	return $this->belongsToMany('App\models\GroupCustomer', 'customer_and_groups', 'customer_id', 'group_id');
    }

    public function notes()
    {
    	return $this->hasMany('App\models\CustomerNote', 'customer_id');
    }

    public function orders()
    {
    	return $this->hasMany('App\models\Order', 'cus_id');
    }

    public function reports()
    {
        return CustomerReport::where('fb_id_cus', $this->fb_id_cus)
                                ->orWhere(function ($query) {
                                    $query->where('email_cus', '=', $this->email_cus)
                                        ->whereNotNull('email_cus');
                                })
                                ->orWhere(function($query2) {
                                    $query2->whereNotNull('phone_cus')
                                            ->where('phone_cus', $this->phone_cus);
                                });
    }

    public function pages()
    {
        return $this->belongsToMany('App\models\Page', 'customer_pages', 'customer_id', 'page_id');
    }

    public function payments()
    {
        return $this->hasMany('App\models\Payment', 'customer_id');
    }
}
