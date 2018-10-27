<?php

namespace App\models\Conversation;

use Illuminate\Database\Eloquent\Model;

class PostPage extends Model
{
    protected $fillable = [
    	'user_id', 'fb_post_id', 'fb_page_name', 'fb_page_id', 'created_time', 'message', 'picture', 'updated_time'
    ];

    public $timestamps = false;
}
