<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FacebookPostPage extends Model
{
    protected $fillable = [
    	'page_id', 'post_id', 'article_id'
    ];
}
