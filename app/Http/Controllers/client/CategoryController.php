<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Category;
use App\models\CateProduct;
use Auth;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
    	$request->validate(
    		[
                'cate_name' => 'required|max:190',
                'cate_des' => 'nullable|max:190'
            ],
            [
                'cate_name.required' => 'Vui lòng điền tên danh mục',
                'cate_name.max' => 'Tên danh mục quá dài',
                'cate_des.max' => 'Mô tả cho danh mục quá dài',
            ]
    	);
        $user = Auth::user();
    	$cateName = $request->cate_name;
    	$des = $request->cate_des;
        $newCate = Category::create([
            'cate_name' => $cateName,
            'cate_des' => $des,
            'user_id' => $user->adminId(),
            'staff_id' => $user->id
        ]);

    	return response()->json([
    		'created' => true,
            'data' => $newCate
    	]);
    }

    public function getAll()
    {
        return Auth::user()->catesUser()
                            ->orderBy('cate_name')
                            ->get()
                            ->toJson();
    }

    public function getListCates(Request $request)
    {
        $keyword = $request->keyword;
        return Auth::user()
                    ->catesUser()->where('cate_name', "LIKE", "%$keyword%")
                    ->withCount('products')
                    ->with(['products' => function($query) {
                        $query->select('prod_code')->withCount('orders');
                    }])
                    ->paginate(10)
                    ->toJson();
    }

    public function getCate($id)
    {
        $cate = Auth::user()->catesUser() 
                            ->where('id', $id)
                            ->first()
                            ->toJson();
        return $cate;
    }

    public function updateCate($id, Request $request)
    {
        $request->validate(
            [
                'cate_name' => 'required|max:190',
                'cate_des' => 'nullable|max:190'
            ],
            [
                'cate_name.required' => 'Vui lòng điền tên danh mục',
                'cate_name.max' => 'Tên danh mục quá dài',
                'cate_des.max' => 'Mô tả cho danh mục quá dài',
            ]
        );
        $cateName = $request->cate_name;
        $des = $request->cate_des;
        $cate = Auth::user()->catesUser()
                            ->where('id', $id)
                            ->first();
        if ($cate) {
            $cate->cate_name = $cateName;
            $cate->cate_des = $des;
            $cate->save();
            return response()->json([
                'updated' => true
            ]);
        }
        return response()->json([
            'updated' => false
        ]);
    }

    public function destroyCate($id)
    {
        $cate = Auth::user()->catesUser()
                            ->where('id', $id)
                            ->first();
        if ($cate) {
            $cateProd = CateProduct::where('cate_id', $id)->get();
            foreach ($cateProd as $item) {
                $item->delete();
            }

            $cate->delete();

            return response()->json([
                'deleted' => true
            ]);
        }
        return response()->json([
            'deleted' => false,
            'message' => 'Không tìm thấy danh mục'
        ]);
    }

    public function getQuantityProduct($id)
    {
        $cate = Auth::user()->catesUser()
                            ->where('id', $id)
                            ->first();
        if ($cate) {
            $productQuantity = $cate->products()->count();
            return response()->json([
                'quantity' => $productQuantity
            ]);
        }
        return response()->json([
            'errors' => true,
            'message' => 'Không tìm thấy danh mục'
        ]);
    }

}
