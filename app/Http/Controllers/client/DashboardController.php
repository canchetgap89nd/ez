<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class DashboardController extends Controller
{
	public function getDashboard()
	{
		$user = Auth::user();
		$page = $user->pages()->where('active', 1)->first();
        if ($page) {
            return view('client.layout');
        }
    	return redirect()->route('set.info');
	}
}
