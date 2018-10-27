<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\RedirectLink;

class RedirectController extends Controller
{
	public function getCreateRedirectLink()
	{
		return view('admin.create_redirect');
	}

    public function createRedirectLink(Request $request)
   	{
   		$request->validate([
   			'from_link' => 'required|max:500',
   			'to_link' => 'required|max:500'
   		]);
   		$from_link = base64_encode($request->from_link);
   		$to_link = base64_encode($request->to_link);
   		RedirectLink::create([
   			'from_link' => $from_link,
   			'to_link' => $to_link
   		]);
   		return redirect()->route('redirect302')->with([
   			'message' => 'Tạo mới chuyển hướng thành công',
   			'status' => 'success'
   		]);
   	}

   	public function index(Request $request)
   	{
   		$keyword = $request->keyword;
   		$links = RedirectLink::paginate(10);
   		return view('admin.redirect_302', compact('links', 'keyword'));
   	}

   	public function editRedirectLink($id)
   	{
   		$link = RedirectLink::find($id);
   		if ($link) {
   			return view('admin.edit_redirect', compact('link'));
   		}
   		return redirect()->route('redirect302')->with([
   			'message' => 'Không tìm thấy link',
   			'status' => 'error'
   		]);
   	}

   	public function updateRedirectLink($id, Request $request)
   	{
   		$request->validate([
   			'from_link' => 'required|max:500',
   			'to_link' => 'required|max:500'
   		]);
   		$link = RedirectLink::find($id);
   		if ($link) {
   			$link->update([
   				'from_link' => base64_encode($request->from_link),
   				'to_link' => base64_encode($request->to_link)
   			]);
   			return redirect()->route('redirect302')->with([
	   			'message' => 'Cập nhật chuyển hướng thành công',
	   			'status' => 'success'
	   		]);
   		}
   		return redirect()->route('redirect302')->with([
   			'message' => 'Không tìm thấy link',
   			'status' => 'error'
   		]);
   	}

   	public function destroyRedirectLink($id)
   	{
   		$link = RedirectLink::find($id);
   		if ($link) {
   			$link->delete();
   			return redirect()->route('redirect302')->with([
	   			'message' => 'Xóa thành công chuyển hướng',
	   			'status' => 'success'
	   		]);
   		}
   		return redirect()->route('redirect302')->with([
   			'message' => 'Không tìm thấy link',
   			'status' => 'error'
   		]);
   	}
}
