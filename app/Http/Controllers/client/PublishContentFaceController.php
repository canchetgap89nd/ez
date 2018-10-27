<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\models\FacebookPost;
use App\models\FacebookPostPage;
use Illuminate\Support\Facades\DB;

class PublishContentFaceController extends Controller
{
    public function publishPost(Request $request)
    {
    	$request->validate(
    		[
    			'pages' => 'required|array',
                'posts' => 'required|array',
                'scheduled_publish_time' => 'required|numeric',
                'is_published' => 'required|boolean',
                'type' => 'required|in:1,2'
    		],
    		[
    			'pages.required' => 'Vui lòng chọn fanpage',
    			'posts.required' => 'Vui lòng thêm bài viết'
            ]
    	);
    	$user = Auth::user();
        $posts = $request->posts;
        $message = _substr($request->message, 150);
        $newPost = FacebookPost::create([
            'user_id' => $user->id,
            'message' => $message,
            'scheduled_publish_time' => $request->scheduled_publish_time,
            'is_published' => $request->is_published,
            'type' => $request->type
        ]);
        $itemPostPage = [];
        foreach ($request->pages as $key => $pageId) {
            $item = [
                'page_id' => $pageId,
                'post_id' => $newPost->id,
                'article_id' => $posts[$key]
            ];
            array_push($itemPostPage, $item);
        }
        FacebookPostPage::insert($itemPostPage);
		return response()->json([
			'success' => 1
		]);
    }

    public function getListPublishPost()
    {
        $user = Auth::user();
        $listPosts = FacebookPost::with('pages')
                                ->where('user_id', $user->id)
                                ->where(function($query) {
                                    $query->where("is_published", true)
                                            ->orWhere(function($query2) {
                                                $query2->where('is_published', false)
                                                        ->where('scheduled_publish_time', '<=', time());
                                            });
                                })
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
        return response()->json([
            'data' => $listPosts
        ]);
    }

    public function getListSchedulePost()
    {
        $user = Auth::user();
        $listPosts = FacebookPost::with('pages')
                                    ->where('user_id', $user->id)
                                    ->where('is_published', false)
                                    ->where('scheduled_publish_time', '>', time())
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);
        return response()->json([
            'data' => $listPosts
        ]);
    }

    public function destroyPostOnFacebook($fbPostId, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->delete('/'.$fbPostId, array(), $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
          return [
            'success' => false,
            'message' => 'Graph returned an error: ' . $e->getMessage()
          ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
          return [
            'success' => false,
            'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
          ];
        }
        return $response->getDecodedBody();
    }

    public function destroyPost ($id)
    {
        $user = Auth::user();
        $article = FacebookPost::where('user_id', $user->id)->where('id', $id)->first();
        if ($article) {
            $pages = $article->pages()->get();
            $items = [];
            $errors = [];
            foreach ($pages as $page) {
                array_push($items, $page->pivot->id);
                $res = $this->destroyPostOnFacebook($page->pivot->article_id, $page->page_token);
                if (!$res['success']) {
                    array_push($errors, $res['message']);
                }
            }
            DB::table('facebook_post_pages')->whereIn("id", $items)->delete();
            $article->delete();
            return response()->json([
                'success' => 1,
                'message' => 'Destroy success',
                'errors' => $errors
            ]);
        }
        return response()->json([
            'success' => 0,
            'message' => 'cannot find post'
        ]);
    }

    public function editPost ($id)
    {
        $user = Auth::user();
        $article = FacebookPost::with('pages')->where('user_id', $user->id)->where('id', $id)->first();
        if ($article) {
            $pages = $article->pages()->get();
            $infoPost = null;
            foreach ($pages as $page) {
                // if article is status and photo
                if ($article->type == 1) {
                    $res = $this->getPostFromFacebook($page->pivot->article_id, $page->page_token);
                } else {
                    // if article is video
                    $res = $this->getVideoFromFacebook($page->pivot->article_id, $page->page_token);
                }
                if ($res['success']) {
                    $infoPost = $res['data'];
                    break;
                }
            }

            return response()->json([
                'success' => 1,
                'data' => $infoPost,
                'article' => $article
            ]);
        }
        return response()->json([
            'success' => 0,
            'message' => 'cannot find post'
        ]);
    }

    public function getPostFromFacebook ($fbPostId, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->get(
                '/'. $fbPostId . '?fields=caption,description,from,full_picture,is_hidden,is_published,message,picture,source,status_type,type,created_time,updated_time',
                $token
            );
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => false,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => false,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return [
            'success' => true,
            'data' => $response->getDecodedBody()
        ];
    }

    public function updatePost ($id, Request $request)
    {
        $user = Auth::user();
        $article = FacebookPost::where('user_id', $user->id)
                                ->where('id', $id)
                                ->first();
        if ($article) {
            $message = _substr($request->message, 150);
            $pages = $article->pages()->get();
            $article->update([
                'message' => $message
            ]);
            $errors = [];
            foreach ($pages as $page) {
                if ($article->type == 1) {
                    // if is status or photo
                    $res = $this->updatePostOnFacebook($page->pivot->article_id, $page->page_token, $request->message);
                } else {
                    // if is video
                    $res = $this->updateVideoOnFacebook($page->pivot->article_id, $page->page_token, $request->message);
                }
                if (!$res['success']) {
                    array_push($errors, $res['message']);
                }
            }
            return [
                'success' => 1,
                'message' => 'Update success',
                'errors' => $errors
            ];
        }
        return [
            'success' => 0,
            'message' => 'cannot find article'
        ];
    }

    public function updatePostOnFacebook ($fbPostId, $token, $message)
    {
        $fb = setupApi();
        try {
            $response = $fb->post('/'.$fbPostId, array('message' => $message), $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
          return [
            'success' => false,
            'message' => 'Graph returned an error: ' . $e->getMessage()
          ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
          return [
            'success' => false,
            'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
          ];
        }
        return $response->getDecodedBody();
    }

    public function getVideoFromFacebook ($fbPostId, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->get(
                '/'. $fbPostId . '?fields=from,published,source,title,description,created_time,updated_time',
                $token
            );
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => false,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => false,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return [
            'success' => true,
            'data' => $response->getDecodedBody()
        ];
    }

    public function updateVideoOnFacebook ($fbPostId, $token, $message)
    {
        $fb = setupApi();
        try {
            $response = $fb->post('/'.$fbPostId, array('description' => $message), $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
          return [
            'success' => false,
            'message' => 'Graph returned an error: ' . $e->getMessage()
          ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
          return [
            'success' => false,
            'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
          ];
        }
        return $response->getDecodedBody();
    }

    public function publishPostNowOnFacebook ($fbPostId, $token, $is_published)
    {
        $fb = setupApi();
        try {
            $response = $fb->post('/'.$fbPostId, array('is_published' => $is_published), $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
          return [
            'success' => false,
            'message' => 'Graph returned an error: ' . $e->getMessage()
          ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
          return [
            'success' => false,
            'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
          ];
        }
        return $response->getDecodedBody();
    }

    public function publishPostNow ($id, Request $request)
    {
        $request->validate([
            'is_published' => 'required|boolean'
        ]);
        $user = Auth::user();
        $article = FacebookPost::where('user_id', $user->id)
                                ->where('id', $id)
                                ->first();
        if ($article) {
            $pages = $article->pages()->get();
            $article->update([
                'is_published' => $request->is_published
            ]);
            $errors = [];
            foreach ($pages as $page) {
                $res = $this->publishPostNowOnFacebook($page->pivot->article_id, $page->page_token, $request->is_published);
                if (!$res['success']) {
                    array_push($errors, $res['message']);
                }
            }
            return [
                'success' => 1,
                'message' => 'Published success!',
                'errors' => $errors
            ];
        }
        return [
            'success' => 0,
            'message' => 'cannot find article'
        ];
    }
}
