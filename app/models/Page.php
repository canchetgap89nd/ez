<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'fb_page_id', 'user_id', 'page_token', 'page_name', 'active', 'page_category', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'user_id'
    ];

    public function user()
    {
    	return $this->belongsTo("App\User", "user_id");
    }

    public function customers()
    {
    	return $this->belongsToMany('App\models\Customer', 'customer_pages', 'page_id', 'customer_id');
    }

    public function isActive()
    {
        if ($this->active) {
            return true;
        }
        return false;
    }

}
