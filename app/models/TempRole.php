<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TempRole extends Model
{
    protected $fillable = [
    	'user_name_parent', 'user_parent', 'user_staff', 'user_name_staff', 'fb_page_id', 'role_id', 'page_name', 'fb_user_staff', 'fb_user_parent'
    ];
}
