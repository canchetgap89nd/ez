<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\CategoryBlog;
use App\Http\Requests\CategoryBlogRequest;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        if ($keyword) {
            $cates = CategoryBlog::where('cate_name', 'like', "%$keyword%")->paginate(10)->withPath('?keyword='. $keyword);
        } else {
	       $cates = CategoryBlog::orderBy('created_at', 'desc')->paginate(10);
        }
    	return view('admin.category', compact('cates', 'keyword'));
    }

    public function add()
    {
    	$cates = CategoryBlog::where('cate_parent', 0)->get();
    	return view('admin.create_cate', compact('cates'));
    }

    public function create(CategoryBlogRequest $request)
    {
    	$catePrent = $request->cate_parent ? $request->cate_parent : 0;
        $cateOrder = $request->cate_order ? $request->cate_order : 0;
        $cateActive = intval($request->cate_active) == 1 ? true : false;
    	$newCate = CategoryBlog::create([
    		'cate_name' => $request->cate_name,
    		'cate_desc' => $request->cate_desc,
    		'cate_parent' => $catePrent,
            'cate_order' => $cateOrder,
            'cate_active' => $cateActive,
            'cate_keyword' => $request->cate_keyword
    	]);
    	$newCate->slug()->create([
    		'slug' => $request->cate_slug,
    		'entity_type' => ENTITY_CATE
    	]);
    	return redirect()->route('blog.all.category')->with([
            'status' => 'success',
            'message' => 'Category created'
        ]);
    }

    public function edit($id)
    {
        $cate = CategoryBlog::find($id);
        if (!$cate) {
            return redirect()->route('blog.all.category');
        }
        $cates = CategoryBlog::where('cate_parent', 0)->get();
        return view('admin.edit_cate', compact('cate', 'cates'));
    }

    public function update($id, CategoryBlogRequest $request)
    {
        $cate = CategoryBlog::find($id);
        if (!$cate) {
            return redirect()->route('blog.all.category');
        }
        $catePrent = $request->cate_parent ? $request->cate_parent : $cate->cate_parent;
        $cate->update([
            'cate_name' => $request->cate_name,
            'cate_parent' => $catePrent,
            'cate_desc' => $request->cate_desc,
            'cate_active' => $request->cate_active,
            'cate_order' => $request->cate_order,
            'cate_keyword' => $request->cate_keyword
        ]);
        $cate->slug()->update([
            'slug' => $request->cate_slug
        ]);
        return redirect()->route('blog.all.category')->with([
            'message' => 'Category Updated',
            'status' => 'success'
        ]);
    }

    public function destroy($id)
    {
        $cate = CategoryBlog::find($id);
        if (!$cate) {
            return redirect()->route('blog.all.category')->with([
                'message' => 'Category not exist',
                'status' => 'error'
            ]);
        }
        //delete category childs
        $childs = CategoryBlog::where('cate_parent', $cate->id)->get();
        foreach ($childs as $child) {
            $child->slug()->first()->delete();
            $child->delete();
        }
        //delete category
        $cate->slug()->first()->delete();
        $cate->delete();
        return redirect()->route('blog.all.category')->with([
            'status' => 'success',
            'message' => 'Category deleted'
        ]);
    }


}
