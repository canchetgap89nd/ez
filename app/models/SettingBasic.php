<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SettingBasic extends Model
{
	protected $fillable = [
		'auto_comment', 'auto_inbox', 'content_comment', 'content_inbox', 'user_id' , 'hide_all_cmt', 'hide_cmt_has_phone', 'hide_cmt_has_key' ,'key_cmt_hide', 'auto_like', 'ping_notify' ,'priority_cmt_has_key', 'key_cmt_priority' ,'filter_email' ,'filter_phone', 'staff_id', 'has_time_inbox', 'has_time_comment' ,'time_start_comment', 'time_end_comment' ,'time_start_inbox', 'time_end_inbox'
	];
}
