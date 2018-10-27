<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\SlugBlog;
use App\models\PostBlog;
use App\models\CategoryBlog;

class PostController extends Controller
{
    public function index($slug)
    {
        $realTime = date('Y-m-d H:i:s', time());
    	$slugObj = SlugBlog::where('slug', $slug)
    						->where('entity_type', ENTITY_POST)
    						->first();
    	if (!$slugObj) {
    		return response()->view('errors.404', [], 404);
    	}
    	$post = PostBlog::where('id', $slugObj->entity_id)
                        ->where('post_time_schedule', '<=', $realTime)
                        ->where('post_active', 1)
    					->where('is_draft', false)
    					->first();
    	if (!$post) {
    		return view('blog.not_found');
    	}
    	$cate2 = CategoryBlog::find($post->post_cate2);
    	$cate1 = CategoryBlog::find($post->post_cate1);
    	if (!$cate2) {
    		$relatePosts = PostBlog::where('post_cate1', $post->post_cate1)
    								->where('is_draft', false)
                                    ->where('post_time_schedule', '<=', $realTime)
                                    ->where('post_active', 1)
    								->orderBy('post_time_schedule', 'desc')
    								->get()
    								->take(3);
    	} else {
    		$relatePosts = PostBlog::where('post_cate2', $post->post_cate2)
    								->where('is_draft', false)
                                    ->where('post_time_schedule', '<=', $realTime)
                                    ->where('post_active', 1)
    								->orderBy('post_time_schedule', 'desc')
    								->get()
    								->take(3);
    	}
    	return view('blog.post', compact('post', 'cate1', 'cate2' , 'relatePosts'));
    }

    public function upViews($id)
    {
    	$post = PostBlog::find($id);
    	if ($post) {
    		$post->update([
    			'views' => $post->views + 1
    		]);

    		return response()->json([
	    		'success' => 1
	    	]);
    	}
    	return response()->json([
    		'success' => 0
    	]);
    }
}
