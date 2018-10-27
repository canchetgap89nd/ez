<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AuthController extends Controller
{
	public function prepareLogin()
	{
		if (Auth::guard('admin')->check()) {
			return redirect()->route('admin.dashboard');
		}
    	return view('admin.login');
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required'
		]);

		$credentials = $request->only('email', 'password');

		if (Auth::guard('admin')->attempt($credentials)) {
			// make session for folder uploads tinymce
			$_SESSION["RF"]["subfolder"] = Auth::guard('admin')->id();
			return redirect()->route('admin.dashboard');
        }
        return redirect()->route('login.admin');
	}

	public function logout()
	{
		Auth::guard('admin')->logout();
		return redirect()->route('login.admin');
	}
}
