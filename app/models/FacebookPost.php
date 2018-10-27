<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FacebookPost extends Model
{
    protected $fillable = [
    	'user_id', 'page_id', 'post_id', 'message', 'scheduled_publish_time', 'is_published', 'type'
    ];

    public function pages()
    {
    	return $this->belongsToMany('App\models\Page', 'facebook_post_pages', 'post_id', 'page_id')->withPivot('article_id', 'post_id', 'id');
    }
}
