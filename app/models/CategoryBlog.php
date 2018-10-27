<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\PostBlog;
use App\models\IntroSlug;

class CategoryBlog extends Model
{
    protected $fillable = [
    	'cate_name', 'cate_desc', 'cate_parent', 'cate_active', 'cate_order', 'cate_option', 'cate_keyword'
    ];

    public function childs()
    {
    	return $this->hasMany('App\models\CategoryBlog', 'cate_parent');
    }

    public function parent()
    {
    	return $this->belongsTo('App\models\CategoryBlog', 'cate_parent');
    }

    public function slug()
    {
    	return $this->hasOne('App\models\SlugBlog', 'entity_id')->where('entity_type', ENTITY_CATE);
    }

    public function slugTxt()
    {
        $slug = $this->slug()->first();
        return $slug ? $this->namespaceSlug() . '/' . $slug->slug : '';
    }

    public function namespaceSlug()
    {
        $intro = IntroSlug::where('type', ENTITY_CATE)->first();
        return $intro ? $intro->namespace : '';
    }

    public function posts()
    {
        return PostBlog::where('post_cate1', $this->id)
                        ->orWhere('post_cate2', $this->id);
    }
}
