<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PluginController extends Controller
{
    public function fbChatDemo(Request $request)
    {
    	$fanpage = $request->page;
    	return view('demo.fbchat', compact('fanpage'));
    }

    public function boxFbChatDemo(Request $request)
    {
    	$fanpage = $request->page;
    	$title = $request->title;

    	return view('demo.boxchat', compact('fanpage', 'title'));
    }
}
