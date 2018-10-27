<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PostPage extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'post_pages';
    protected $fillable = [
    	'user_id', 'fb_post_id', 'fb_page_name', 'fb_page_id', 'created_time', 'message', 'picture', 'page_id', 'updated_time', 'is_hidden', 'type'
    ];

    public function comments()
    {
    	return $this->hasMany('App\models\Mongo\Comment', 'post_id');
    }
}
