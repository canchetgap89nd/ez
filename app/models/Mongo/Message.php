<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Message extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'messages';
    protected $fillable = [
    	'user_id', 'conver_id', 'fb_message_id', 'from_id', 'message', 'from_name', 'customer_id', 'has_phone', 'has_key', 'unread', 'created_time', 'staff_reply_id', 'page_id', 'fb_page_id', 'from_platform'
    ];

    public function attachments()
	{
		return $this->hasMany('App\models\Mongo\Attachment', 'entity_id')->where('entity_type', 'MESSAGE');
	}
}
