<?php 

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\models\PostBlog;

class PostMostViewComposer
{

	public function compose(View $view)
	{
		$posts = PostBlog::where('is_draft', false)
							->orderBy('views', 'desc')
							->get()
							->take(6);
		$view->with('postMostView', $posts);
	}
}