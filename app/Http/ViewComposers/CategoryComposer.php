<?php 

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\models\CategoryBlog;

class CategoryComposer
{

	public function compose(View $view)
	{
		$cates = CategoryBlog::where('cate_active', 1)
							->where("cate_parent", 0)
							->with(['childs' => function ($query) {
								$query->orderBy('cate_order', 'desc');
							}])
							->orderBy('cate_order', 'desc')
							->get()
							->take(3);
		$view->with('categories', $cates);
	}
}