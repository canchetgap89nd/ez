<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class CustomerReport extends Model
{
    protected $table = "customer_reports";

    protected $fillable = ['id_user_reported', 'name_cus', 'phone_cus', 'email_cus', 'fb_id_cus', 'title_report', 'des_report', 'user_id'];

    protected $hidden = [
    	'id_user_reported'
    ];
}
