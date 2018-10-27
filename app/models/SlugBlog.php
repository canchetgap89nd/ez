<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SlugBlog extends Model
{
    protected $fillable = [
    	'slug', 'entity_type', 'entity_id'
    ];

    public function post()
    {
    	return $this->belongsTo("App\models\PostBlog", "entity_id")->where("entity_type", ENTITY_POST);
    }

    public function cate()
    {
    	return $this->belongsTo("App\models\PostBlog", "entity_id")->where("entity_type", ENTITY_CATE);
    }
}
