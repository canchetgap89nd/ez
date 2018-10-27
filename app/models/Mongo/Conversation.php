<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Conversation extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'conversations';
    protected $fillable = [
    	'user_id', 'from_id', 'from_name', 'page_id', 'thread_id', 'last_message', 'customer_id', 'type', 'updated_time', 'post_id', 'unreply', 'fb_page_id', 'has_phone', 'unread', 'has_order', 'has_note', 'has_key', 'is_multiple_chat', 'tranfer'
    ];

    public function comments()
    {
        return $this->hasMany('App\models\Mongo\Comment', 'conver_id');
    }

    public function commentsUnread()
    {
        return $this->hasMany('App\models\Mongo\Comment', 'conver_id')->where('unread', true);
    }

    public function commentsHasPhone()
    {
        return $this->hasMany('App\models\Mongo\Comment', 'conver_id')->where('has_phone', true);
    }

    public function commentsHasKey()
    {
        return $this->hasMany('App\models\Mongo\Comment', 'conver_id')->where('has_key', true);
    }

    public function commentsParent()
    {
        return $this->hasMany('App\models\Mongo\Comment', 'conver_id')->whereNull('parent_id');
    }

    public function messages()
    {
        return  $this->hasMany('App\models\Mongo\Message', 'conver_id');
    }

    public function messagesUnread()
    {
        return $this->hasMany('App\models\Mongo\Message', 'conver_id')->where('unread', true);
    }

    public function messagesHasPhone()
    {
        return $this->hasMany('App\models\Mongo\Message', 'conver_id')->where('has_phone', true);
    }

    public function messagesHasKey()
    {
        return $this->hasMany('App\models\Mongo\Message', 'conver_id')->where('has_key', true);
    }
}
