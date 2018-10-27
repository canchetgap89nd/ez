<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\SlugBlog;

class GeneralController extends Controller
{
    public function generateSlug(Request $request)
    {
    	$request->validate([
    		'str' => 'required',
    		'type' => 'required|in:1,2'
    	]);
    	$str = $request->str;
    	$type = $request->type;
    	$slug = str_slug($str, '-');
    	if (intval($type) === 1) {
    		$slugHas = SlugBlog::where('entity_type', 1)->where('slug', $slug)->first();
    		if ($slugHas) {
	    		return response()->json([
	    			'success' => 0,
	    			'message' => 'Slug is use'
	    		]);
    		}
    	} else 
    	if (intval($type) === 2) {
    		$slug = $slug . '-' . str_slug(date('d-m-Y', time())) . '.html';
    		$slugHas = SlugBlog::where('entity_type', 2)->where('slug', $slug)->first();
    		if ($slugHas) {
	    		return response()->json([
	    			'success' => 0,
	    			'message' => 'Slug is use'
	    		]);
    		}
    	}

    	return response()->json([
    		'success' => 1,
    		'slug' => $slug
    	]);
    }
}
