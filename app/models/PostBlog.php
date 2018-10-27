<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\IntroSlug;

class PostBlog extends Model
{
    protected $fillable = [
    	'post_title', 'auth_id', 'post_desc', 'post_content', 'post_thumb', 'post_time_schedule', 'post_cate1', 'post_cate2', 'views', 'is_draft', 'post_keyword', 'post_active'
    ];

    public function cate1()
    {
    	return $this->belongsTo('App\models\CategoryBlog', 'post_cate1');
    }

    public function cate2()
    {
        return $this->belongsTo('App\models\CategoryBlog', 'post_cate2');
    }

    public function slug()
    {
    	return $this->hasOne('App\models\SlugBlog', 'entity_id')->where('entity_type', ENTITY_POST);
    }

    public function slugTxt()
    {
        $slug = $this->slug()->first();
        return $slug ? $this->namespaceSlug() . '/' . $slug->slug : '';
    }

    public function namespaceSlug()
    {
        $intro = IntroSlug::where('type', ENTITY_POST)->first();
        return $intro ? $intro->namespace : '';
    }

    public function author()
    {
        return $this->belongsTo('App\models\UserAdmin', 'auth_id');
    }
}
