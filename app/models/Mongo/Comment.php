<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'comments';
    protected $fillable = [
    	'user_id', 'conver_id', 'fb_comment_id', 'parent_id', 'from_id', 'from_name', 'customer_id', 'message', 'can_comment', 'can_hide', 'can_like', 'can_remove', 'can_reply_privately', 'like_count', 'is_hidden', 'user_likes', 'is_remove', 'has_phone', 'has_key', 'unread', 'created_time', 'staff_reply_id', 'post_id', 'page_id', 'fb_page_id', 'from_platform'
    ];

    public function childs()
	{
		return $this->hasMany('App\models\Mongo\Comment', 'parent_id');
	}

	public function attachment()
	{
		return $this->hasOne('App\models\Mongo\Attachment', 'entity_id')->where('entity_type', 'COMMENT');
	}
}
