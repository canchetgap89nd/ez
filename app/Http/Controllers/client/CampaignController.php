<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\client\ProductController;
use Auth;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\models\Campaign;
use App\models\CampaignProduct;
use Illuminate\Support\Facades\DB;
use App\User;

class CampaignController extends ProductController
{
    public function createCampaign(CreateCampaignRequest $request)
    {
    	$name = $request->name;
    	$discount = $request->discount;
    	$typeTime = $request->typeTime;
    	$products = $request->products;
    	$startTime = $request->startTime;
    	$endTime = $request->endTime;
        $user = User::find(Auth::id());

    	$newCampId = $this->createCampCore($name, $user, $discount, $typeTime, $startTime, $endTime);

    	foreach ($products as $item) {
    		$prod = $user->productUsers()
							->find($item);
    		if ($prod) {
    			$this->createCampProducts($newCampId, $prod->id);
    		}
    	}

    	return response()->json([
    		'created' => true
    	]);
    }

    public function createCampProducts(int $campId, int $prodId)
    {
    	$newCampProd = new CampaignProduct;
    	$newCampProd->camp_id = $campId;
    	$newCampProd->prod_id = $prodId;
    	$newCampProd->save();
    	return $newCampProd->id;
    }

    public function deleteCampProducts($campId, $prodId)
    {
    	$campProd = CampaignProduct::where('camp_id', $campId)
    								->where('prod_id', $prodId)
    								->first();
    	$countProductOfCamp = CampaignProduct::where('camp_id', $campId)->count();
    	if ($campProd && $countProductOfCamp > 1) {
			$campProd->delete();
    	}
    }

    public function createCampCore($name, User $user, $discount, $typeTime, $startTime = null, $endTime = null)
    {
    	$newCamp = new Campaign;
    	$newCamp->camp_name = $name;
    	$newCamp->user_id = $user->adminId();
        $newCamp->staff_id = $user->id;
    	$newCamp->perc_disc = $discount;
    	if ($typeTime == 2) {
    		$newCamp->sold_out = true;
    	} else
    	if ($typeTime == 1) {
    		$newCamp->sold_out = false;
    		$newCamp->start_time = $startTime;
    		$newCamp->end_time = $endTime;
    	}
    	$newCamp->save();
    	return $newCamp->id;
    }

    public function updateCampaignCore($camp, array $data)
    {
        $name = isset($data['name']) && $data['name'] ? $data['name'] : null;
        $discount = isset($data['discount']) && ($data['discount'] >= 0) ? $data['discount'] : null;
        $startTime = isset($data['startTime']) && $data['startTime'] ? $data['startTime'] : null;
        $endTime = isset($data['endTime']) && $data['endTime'] ? $data['endTime'] : null;
        $typeTime = isset($data['typeTime']) && $data['typeTime'] ? $data['typeTime'] : null;
        $countSell = isset($data['countSell']) && $data['countSell'] >= 0 ? $data['countSell'] : null;
        $revenue = isset($data['revenue']) && $data['revenue'] >= 0 ? $data['revenue'] : null;
        $spentMoney = isset($data['spentMoney']) && $data['spentMoney'] >= 0 ? $data['spentMoney'] : null;
        $status = isset($data['status']) ? $data['status'] : null;

    	$camp->camp_name = $name !== null ? $name : $camp->camp_name;
    	$camp->perc_disc = $discount !== null ? $discount : $camp->perc_disc;
    	if ($typeTime == 2) {
    		$camp->sold_out = true;
    	} else
    	if ($typeTime == 1) {
    		$camp->sold_out = false;
    		$camp->start_time = $startTime !== null ? $startTime : $camp->start_time;
    		$camp->end_time = $endTime !== null ? $endTime : $camp->end_time;
    	} else 
        if ($typeTime === null && !$camp->sold_out) {
            $camp->start_time = $startTime !== null ? $startTime : $camp->start_time;
            $camp->end_time = $endTime !== null ? $endTime : $camp->end_time;
        }
        $camp->count_sell = $countSell !== null ? $countSell : $camp->count_sell;
        $camp->revenue = $revenue !== null ? $revenue : $camp->revenue;
        $camp->spent_money = $spentMoney !== null ? $spentMoney : $camp->spent_money;
        $camp->status = $status !== null ? $status : $camp->status;
    	$camp->save();
    	return $camp->id;
    }

    public function getListCampaign(Request $request)
    {
    	$keyword = $request->keyword; 
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $status = $request->status;

        $arrWhere = [
            ['camp_name', 'like', "%$keyword%"]
        ];

        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['created_at', '>=', $timeFrom]);

        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['created_at', '<=', $timeTo]);
        }

    	return Auth::user()
					->campaignsUser()
                    ->where($arrWhere)
					->orderBy('created_at', 'desc')
					->paginate(10)
					->toJson();
    }

    public function getCampaign($id)
    {
    	$camp = Auth::user()->campaignsUser()
    						->where('id', $id)
    						->first();
    	if ($camp) {
    		$products = $camp->products()->get();
    		return response()->json([
    			'camp' => $camp,
    			'products' => $products
    		]);
    	}
    	return response()->json([
    		'message' => 'Không tìm thấy sản phẩm'
    	]);
    }

    public function updateCampaign($id, UpdateCampaignRequest $request)
    {
    	$name = $request->name;
    	$discount = $request->discount;
    	$typeTime = $request->typeTime;
    	$startTime = $request->startTime;
    	$endTime = $request->endTime;
    	$products = $request->products;
    	$newProducts = $request->newProducts;
    	$removeProds = $request->removeProds;
    	$user = User::find(Auth::id());

    	$camp = Auth::user()->campaignsUser()->find($id);

    	if ($camp) {
            $dataCamp['name'] = $name;
            $dataCamp['discount'] = $discount;
            $dataCamp['startTime'] = $startTime;
            $dataCamp['endTime'] = $endTime;
            $dataCamp['typeTime'] = $typeTime;
    		$this->updateCampaignCore($camp, $dataCamp);

	    	foreach ($newProducts as $item) {
	    		$prod = $user->productUsers()->find($item);
	    		if ($prod) {
	    			$this->createCampProducts($camp->id, $prod->id);
	    		}
	    	}

	    	foreach ($removeProds as $item) {
	    		$prod = $user->productUsers()->find($item);
	    		if ($prod) {
	    			$this->deleteCampProducts($camp->id, $prod->id);
	    		}
	    	}

	    	return response()->json([
	    		'updated' => true
	    	]);
    	}
    	return response()->json([
    		'updated' => false,
    		'message' => 'Không tìm thấy khuyến mại'
    	]);
    }

    public function pauseCampaign($id)
    {
    	$camp = Auth::user()->campaignsUser()
    						->where('id', $id)
    						->first();

    	if ($camp) {
    		$camp->status = 0;
    		$camp->save();
    		return response()->json([
    			'updated' => true
    		]);
    	}

    	return response()->json([
			'updated' => false,
			'message' => 'Không tìm thấy khuyến mãi'
		]);
    }

    public function runAgainCampaign($id, Request $request)
    {
        $request->validate(
            [
                'startTime' => 'required|date_format:Y-m-d H:i:s',
                'endTime' => 'required|date_format:Y-m-d H:i:s'
            ],
            [
                'startTime.required' => 'Vui lòng chọn thời gian bắt đầu chạy khuyến mãi',
                'endTime.required' => 'Vui lòng chọn thời gian kết thúc khuyến mãi'
            ]
        );
        $startTime = $request->startTime;
        $endTime = $request->endTime;
        $user = User::find(Auth::id());
        $camp = $user->campaignsUser()->find($id);

        if ($camp) {
            $typeTime = $camp->sold_out ? 2 : 1;
            if ($typeTime === 1) {
                if (time() < strtotime($endTime) && strtotime($startTime) < strtotime($endTime) ) {
                    $dataCamp['startTime'] = $startTime;
                    $dataCamp['endTime'] = $endTime;
                    $dataCamp['status'] = 1;
                    $this->updateCampaignCore($camp, $dataCamp);
                    return response()->json([
                        'updated' => true
                    ]);
                }
                return response()->json([
                    'updated' => false,
                    'message' => 'Vui lòng chọn lại thời gian chạy khuyến mãi'
                ]);
            }
            return response()->json([
                'updated' => true
            ]);
        }
        return response()->json([
            'updated' => false,
            'message' => 'Không tìm thấy khuyến mại'
        ]);
    }

    public function exportFileCampaign($type)
    {
        $campaigns = Auth::user()
                            ->campaignsUser()
                            ->get()
                            ->toArray();
        $campaigns = json_decode(json_encode($campaigns), true);

        $header =  ['ID', 'Tên chiến dịch', 'ID người tạo', 'Tỷ lệ chiết khấu', 'Số tiền triết khấu', 'Thời gian bắt đầu', 'Thời gian kết thúc', 'Bán hết (1 - có)', 'Số lượng bán được', 'Doanh thu', 'Số tiền đã chi', 'Trạng thái', 'Thời gian tạo', 'Thời gian cập nhật cuối'];
        array_unshift($campaigns, $header);

        return \Excel::create('campaigns_list', function($excel) use ($campaigns) {

            $excel->sheet('campaigns', function($sheet) use ($campaigns)

            {

                $sheet->fromArray($campaigns, null, 'A1', false, false);

            });

        })->download($type);
    }
}
