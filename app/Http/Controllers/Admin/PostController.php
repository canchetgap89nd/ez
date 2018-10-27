<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\CategoryBlog;
use App\models\PostBlog;
use App\Http\Requests\PostBlogRequest;
use Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $cate = $request->cate;
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        if ($keyword || $cate || $timeFrom) {
            $customUrl = '';
            if ($keyword) {
                $customUrl .= '?keyword=' . $keyword;
                $query = PostBlog::where('post_title', 'like', "%$keyword%");
            }

            if ($cate) {
                if ($keyword) {
                    $customUrl .= '&cate=' . $cate;
                    $query = $query->where('post_cate1', $cate)
                                    ->orWhere('post_cate2', $cate);
                } else {
                    $customUrl .= '?cate=' . $cate;
                    $query = PostBlog::where('post_cate1', $cate)
                                        ->orWhere('post_cate2', $cate);
                }
            }

            if ($timeFrom && $timeTo) {
                if ($keyword || $cate) {
                    $customUrl .= '&timeFrom=' . $timeFrom . '&timeTo=' . $timeTo;
                    $query = $query->where('created_at', '>=', $timeFrom)
                                    ->where('created_at', '<=', $timeTo);
                } else {
                    $customUrl .= '?timeFrom=' . $timeFrom . '&timeTo=' . $timeTo;
                    $query = PostBlog::where('created_at', '>=', $timeFrom)
                                        ->where('created_at', '<=', $timeTo);
                }
            }
            $posts = $query->paginate(10)->withPath($customUrl);
        } else {
            $posts = PostBlog::paginate(10);
        }
        $cates = CategoryBlog::all();
    	return view('admin.post', compact('posts', 'keyword', 'cate', 'cates', 'timeFrom', 'timeTo'));
    }

    public function add()
    {
    	$cates = CategoryBlog::all();
    	return view("admin.create_post", compact('cates'));
    }

    public function create(PostBlogRequest $request)
    {
    	$postThumb = parse_url($request->postThumb, PHP_URL_PATH) . parse_url($request->postThumb, PHP_URL_QUERY);
        $postSchedule = $request->postSchedule ? $request->postSchedule : date('Y-m-d H:i:s', time());
        $isDraft = $request->typeSave == 1 ? false : true;
        $cate = CategoryBlog::find($request->postCate);
        if (!$cate) {
            return redirect()->route('blog.all.post')->with([
                'message' => 'Category not exist',
                'status' => 'error'
            ]);
        }
        $cateParent = $cate->parent()->first();
        $cate1 = $cateParent ? $cateParent->id : $cate->id;
        $cate2 = $cateParent ? $cate->id : 0;
    	$post = PostBlog::create([
    		'post_title' => $request->postTitle,
    		'auth_id' => Auth::guard('admin')->id(),
    		'post_desc' => $request->postDesc,
            'post_keyword' => $request->post_keyword,
    		'post_content' => $request->postContent,
    		'post_thumb' => $postThumb,
    		'post_time_schedule' => $postSchedule,
            'post_cate1' => $cate1,
            'post_cate2' => $cate2,
            'is_draft' => $isDraft
    	]);

    	$post->slug()->create([
    		'entity_type' => ENTITY_POST,
    		'slug' => $request->postSlug
    	]);

    	return redirect()->route("blog.all.post")->with([
            'message' => 'Create Post Success',
            'status' => 'success'
        ]);
    }

    public function edit($id)
    {
        $post = PostBlog::find($id);
        if (!$post) {
            return redirect()->route('blog.all.post')->with([
                'status' => 'error',
                'message' => 'Article not exist'
            ]);
        }
        $cates = CategoryBlog::all();
        return view('admin.edit_post', compact('post', 'cates'));
    }

    public function update($id, PostBlogRequest $request)
    {
        $post = PostBlog::find($id);
        $isDraft = $request->typeSave == 1 ? false : true;
        if (!$post) {
            return redirect()->route('blog.all.post')->with([
                'status' => 'error',
                'message' => 'Article not exist'
            ]);
        }
        $cate = CategoryBlog::find($request->postCate);
        if (!$cate) {
            return redirect()->route('blog.all.post')->with([
                'message' => 'Category not exist',
                'status' => 'error'
            ]);
        }
        $postThumb = parse_url($request->postThumb, PHP_URL_PATH) . parse_url($request->postThumb, PHP_URL_QUERY);
        $postSchedule = $request->postSchedule ? $request->postSchedule : $post->post_time_schedule;
        $cateParent = $cate->parent()->first();
        $cate1 = $cateParent ? $cateParent->id : $cate->id;
        $cate2 = $cateParent ? $cate->id : 0;

        $post->update([
            'post_title' => $request->postTitle,
            'post_desc' => $request->postDesc,
            'post_content' => $request->postContent,
            'post_thumb' => $postThumb,
            'post_time_schedule' => $postSchedule,
            'post_cate1' => $cate1,
            'post_cate2' => $cate2,
            'is_draft' => $isDraft,
            'post_keyword' => $request->post_keyword,
        ]);

        $post->slug()->update([
            'slug' => $request->postSlug
        ]);

        return redirect()->route("blog.all.post")->with([
            'message' => 'Article Updated',
            'status' => 'success'
        ]);
    }

    public function destroy($id)
    {
        $post = PostBlog::find($id);
        if (!$post) {
            return redirect()->route('blog.all.post')->with([
                'status' => 'error',
                'message' => 'Article not exist'
            ]);
        }
        $post->slug()->first()->delete();
        $post->delete();
        return redirect()->route("blog.all.post")->with([
            'message' => 'Article deleted',
            'status' => 'success'
        ]);
    }

    public function active($id)
    {
        $post = PostBlog::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Not find Post',
                'success' => 0
            ]);
        }
        $post->update([
            'post_active' => 1
        ]);
        return response()->json([
            'message' => 'Article is active',
            'success' => 1
        ]);
    }

    public function deActivate($id)
    {
        $post = PostBlog::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Not find Post',
                'success' => 0
            ]);
        }
        $post->update([
            'post_active' => 0
        ]);
        return response()->json([
            'message' => 'Article is deactivate',
            'success' => 1
        ]);
    }
}
