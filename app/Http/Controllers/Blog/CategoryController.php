<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\SlugBlog;
use App\models\CategoryBlog;
use App\models\PostBlog;

class CategoryController extends Controller
{
    public function index($slug)
    {
        $realTime = date('Y-m-d H:i:s', time());
    	$slugObj = SlugBlog::where('slug', $slug)
                            ->where("entity_type", ENTITY_CATE)
                            ->first();
    	if (!$slugObj) {
    		return view('blog.not_found');
    	}
    	$cate = CategoryBlog::find($slugObj->entity_id);
    	if (!$cate) {
    		return view('blog.not_found');
    	}
        $parent = $cate->parent()->first();
    	$posts = PostBlog::where([
                            ['post_cate1', $cate->id],
                            ['is_draft', false],
                            ['post_time_schedule', '<=', $realTime],
                            ['post_active', 1]
                        ])
    					->orWhere([
                            ['post_cate2', $cate->id],
                            ['is_draft', false],
                            ['post_time_schedule', '<=', $realTime],
                            ['post_active', 1]
                        ])
                        ->orderBy('post_time_schedule', 'desc')
    					->paginate(10);
    	return view('blog.category', compact('cate', 'posts', 'parent'));
    }
}
