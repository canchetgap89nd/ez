<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\models\VietNamProvince;
use App\models\VietNamDistrict;
use App\models\VietNamWard;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\client\ConversationController;

class ApiController extends Controller
{
    public function getInfoUser()
    {
    	$user = Auth::user();
    	$mems = $user->accounts()
    					->select('name', 'id')
    					->with('roles', 'pages')
    					->get();
		$role = $user->roles()->first();
		$pages = $user->pages()->where('active', 1)->get();
    	$setting = $user->generalSetting()->first();
    	$groups = $user->groupsCustomer()->get();
    	$quickAns = $user->quickAnswers()->get();
    	if ($setting) {
    		$setting->key_cmt_hide = json_decode(base64_decode($setting->key_cmt_hide));
    		$setting->key_cmt_priority = json_decode(base64_decode($setting->key_cmt_priority));
    	}
    	$package = $user->packagesActive();
    	return response([
    		'user' => $user,
    		'pages' => $pages,
    		'setting' => $setting,
    		'role' => $role,
    		'mems' => $mems,
    		'groups' => $groups,
    		'quickAns' => $quickAns,
    		'package' => $package
    	]);
    }

	public function saveWhitelistedSite(Request $request)
	{
		$request->validate(
			[
				'page_token' => 'required',
				'sites' => 'required|array'
			],
			[
				'page_token.required' => 'Vui lòng chọn Fanpage',
				'sites.required' => 'Vui lòng thêm website'
			]
		);

		$fb = setupApi();

		$sites = $request->sites;
		$token = $request->page_token;
		$data = [
			'whitelisted_domains' => $sites
		];

		try {
		  	// Returns a `Facebook\FacebookResponse` object
	  		$response = $fb->post('/me/messenger_profile', $data, $token);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  echo 'Graph returned an error: ' . $e->getMessage();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		}

		return response()->json($response);
	}

	public function getWhitelistedDomains($token)
	{
		$fb = setupApi();

		try {
		  	// Returns a `Facebook\FacebookResponse` object
	  		$response = $fb->get('/me/messenger_profile?fields=whitelisted_domains', $token);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  echo 'Graph returned an error: ' . $e->getMessage();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		}

		$res = isset($response->getDecodedBody()['data'][0]['whitelisted_domains']) ? $response->getDecodedBody()['data'][0]['whitelisted_domains'] : null;

		return response()->json($res);
	}

	public function getProvinces()
	{
		return VietNamProvince::all()->toJson();
	}

	public function getDistricts($id)
	{
		return VietNamDistrict::where('province_id', $id)->get()->toJson();
	}

	public function getWards($id)
	{
		return VietNamWard::where('district_id', $id)->get()->toJson();
	}

	public function getAddress(Request $request)
	{
		$keyword = $request->keyword;
		$arr = explode(',', $keyword);

		$ward = isset($arr[0]) ? trim($arr[0]) : null;
		$district = isset($arr[1]) ? trim($arr[1]) : null;
		$province = isset($arr[2]) ? trim($arr[2]) : null;

		$address = DB::table('viet_nam_wards')
					->leftJoin('viet_nam_districts', 'viet_nam_wards.district_id', '=', 'viet_nam_districts.id')
					->leftJoin('viet_nam_provinces', 'viet_nam_provinces.id', '=', 'viet_nam_districts.province_id')
					->select('viet_nam_wards.id as ward_id', 'viet_nam_wards.name_ward', 'viet_nam_districts.id as district_id', 'viet_nam_districts.name_district', 'viet_nam_provinces.id as province_id', 'viet_nam_provinces.name')
					->where([
						['viet_nam_wards.name_ward', 'like', "%$ward%"],
						['viet_nam_districts.name_district', 'like', "%$district%"],
						['viet_nam_provinces.name', 'like', "%$province%"]
					])
					->get();

		if (count($address) == 0) {
			$address = DB::table('viet_nam_wards')
					->leftJoin('viet_nam_districts', 'viet_nam_wards.district_id', '=', 'viet_nam_districts.id')
					->leftJoin('viet_nam_provinces', 'viet_nam_provinces.id', '=', 'viet_nam_districts.province_id')
					->select('viet_nam_wards.id as ward_id', 'viet_nam_wards.name_ward', 'viet_nam_districts.id as district_id', 'viet_nam_districts.name_district', 'viet_nam_provinces.id as province_id', 'viet_nam_provinces.name')
					->where('viet_nam_districts.name_district', 'like', "%$keyword%")
					->orWhere('viet_nam_provinces.name', 'like', "%$keyword%")
					->get();
		}

		return $address->toJson();
	}
}
