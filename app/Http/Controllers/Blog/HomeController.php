<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\PostBlog;

class HomeController extends Controller
{
    public function index(Request $request)
    {
    	$keyword = $request->keyword;
        $realTime = date('Y-m-d H:i:s', time());
    	if ($keyword) {
    		$posts = PostBlog::where('is_draft', false)
    						->where('post_title', 'like', "%$keyword%")
                            ->where('post_time_schedule', '<=', $realTime)
                            ->where('post_active', 1)
                            ->orderBy('post_time_schedule', 'desc')
    						->paginate(10)
    						->withPath('?keyword=' . $keyword);
    	} else {
    		$posts = PostBlog::where('post_time_schedule', '<=', $realTime)
                            ->where('post_active', 1)
                            ->orderBy('post_time_schedule', 'desc')
                            ->where('is_draft', false)
                            ->paginate(10);
    	}
    	return view('blog.home', compact('posts', 'keyword'));
    }

    public function notFound()
    {
    	return view('blog.not_found');
    }
}
