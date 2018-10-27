<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\models\Page;
use App\models\Customer;
use App\Respositories\Conversation\ConverRespository;
use App\Respositories\Conversation\CommentRespository;
use App\Respositories\Conversation\MessageRespository;
use App\models\Conversation\PostPage;
use Auth;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function overview(Request $request)
    {
    	$pageId = $request->pageId;
    	$timeFrom = $request->timeFrom;
    	$timeTo = $request->timeTo;
    	$typeCompare = $request->typeCompare;

    	if ($timeFrom) {
    		$timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
    	}
    	if ($timeTo) {
    		$timeTo = date("Y-m-d H:i:s", strtotime('+23 hours 59 minutes 59 seconds', strtotime($timeTo)));
    	}

    	if ($typeCompare == 1) {
    		$timeCompareFrom = $timeFrom ? date('Y-m-d H:i:s', strtotime('-1 month', strtotime($timeFrom))) : null;
    		$timeCompareTo = $timeTo ? date('Y-m-d H:i:s', strtotime('-1 month', strtotime($timeTo))) : null;
    	} else 
    	if ($typeCompare == 2) {
    		$timeCompareFrom = $timeFrom ? date('Y-m-d H:i:s', strtotime('-1 year', strtotime($timeFrom))) : null;
    		$timeCompareTo = $timeTo ? date('Y-m-d H:i:s', strtotime('-1 year', strtotime($timeTo))) : null;
    	}

    	$user = User::find(Auth::id());
        $adminId = $user->adminId();
		$page = Page::where('user_id', $user->id)->find($pageId);

    	$current = [];
    	$compareTo = [];

		$current['comments_count'] = $this->getCommentsCountOfPage($adminId, $timeFrom, $timeTo, $page);
		$current['messages_count'] = $this->getMessagesCountOfPage($adminId, $timeFrom, $timeTo, $page);
		$current['total_value_orders'] = $this->getValueOrdersOfPage($adminId, $timeFrom, $timeTo, $page);
		$current['products_count_sell'] = $this->getProductsCountOfPage($adminId, $timeFrom, $timeTo, $page);
		$current['sort_orders'] = $this->getOrdersTypeOfPage($user, $timeFrom, $timeTo, $page);

		if ($timeCompareFrom || $timeCompareTo) {
			$compareTo['comments_count'] = $this->getCommentsCountOfPage($adminId, $timeCompareFrom, $timeCompareTo, $page);
			$compareTo['messages_count'] = $this->getMessagesCountOfPage($adminId, $timeCompareFrom, $timeCompareTo, $page);
			$compareTo['total_value_orders'] = $this->getValueOrdersOfPage($adminId, $timeCompareFrom, $timeCompareTo, $page);
			$compareTo['products_count_sell'] = $this->getProductsCountOfPage($adminId, $timeCompareFrom, $timeCompareTo, $page);
			$compareTo['sort_orders'] = $this->getOrdersTypeOfPage($user, $timeCompareFrom, $timeCompareTo, $page);
		}

		return response()->json([
			'current' => $current,
			'compareTo' => $compareTo
		]);
    }

    public function getCommentsCountOfPage($adminId, $timeFrom = null, $timeTo = null, Page $page = null)
    {
    	$timeFrom = strtotime($timeFrom);
    	$timeTo = strtotime($timeTo);
        if ($page) {
            $condict = [
                ['user_id', $adminId],
                ['fb_page_id', $page->fb_page_id],
                ['created_time', '>=', $timeFrom],
                ['created_time', '<=', $timeTo]
            ];
    	} else {
            $condict = [
                ['user_id', $adminId],
                ['created_time', '>=', $timeFrom],
                ['created_time', '<=', $timeTo]
            ];
        }
		$count = (new CommentRespository($adminId))->getCount($condict);
    	return $count;
    }

    public function getMessagesCountOfPage($adminId, $timeFrom = null, $timeTo = null, Page $page = null)
    {
    	$timeFrom = strtotime($timeFrom);
    	$timeTo = strtotime($timeTo);
    	if ($page) {
            $condict = [
                ['user_id', $adminId],
                ['fb_page_id', $page->fb_page_id],
                ['created_time', '>=', $timeFrom],
                ['created_time', '<=', $timeTo]
            ];
    	} else {
            $condict = [
                ['user_id', $adminId],
                ['created_time', '>=', $timeFrom],
                ['created_time', '<=', $timeTo]
            ];
        }
		$count = (new MessageRespository($adminId))->getCount($condict);
        return $count;
    }

    public function getValueOrdersOfPage($adminId, $timeFrom = null, $timeTo = null, Page $page = null)
    {
    	if ($page) {
    		$customers = Customer::with('orders')->where('user_id', $adminId)->where('fb_page_id', $page->fb_page_id)->get();
    	} else {
    		$customers = Customer::with('orders')->where('user_id', $adminId)->get();
    	}
		$total = 0;
		foreach ($customers as $customer) {
			$orders = $customer->orders;
			foreach ($orders as $order) {
				$timeCreated = strtotime($order->created_at);
    			$check1 = false;
				if ($timeFrom) {
    				$strTimeFrom = strtotime($timeFrom);
					if ($strTimeFrom <= $timeCreated) {
						$check1 = true;
					}
				} else {
					$check1 = true;
				}

				$check2 = false;
				if ($timeTo) {
    				$strTimeTo = strtotime($timeTo);
    				$strTimeToConfig = strtotime('+23 hours +59 minutes +59 seconds', $strTimeTo);
	    			if ($timeCreated <= $strTimeToConfig) {
	    				$check2 = true;
	    			}
				} else {
					$check2 = true;
				}

				if ($check1 && $check2) {
					$total += $order->total_value;
				}
			}
		}

		return $total;
    }

    public function getProductsCountOfPage($adminId, $timeFrom = null, $timeTo = null, Page $page = null)
    {
    	if ($page) {
    		$customers = Customer::with('orders')->where('user_id', $adminId)->where('fb_page_id', $page->fb_page_id)->get();

    	} else {
    		$customers = Customer::with('orders')->where('user_id', $adminId)->get();
    	}
    	$total = 0;
		foreach ($customers as $customer) {
			$orders = $customer->orders;
			foreach ($orders as $order) {
    			$timeCreated = strtotime($order->created_at);
    			$check1 = false;
				if ($timeFrom) {
    				$strTimeFrom = strtotime($timeFrom);
					if ($strTimeFrom <= $timeCreated) {
						$check1 = true;
					}
				} else {
					$check1 = true;
				}

				$check2 = false;
				if ($timeTo) {
    				$strTimeTo = strtotime($timeTo);
    				$strTimeToConfig = strtotime('+23 hours +59 minutes +59 seconds', $strTimeTo);
	    			if ($timeCreated <= $strTimeToConfig) {
	    				$check2 = true;
	    			}
				} else {
					$check2 = true;
				}

				if ($check1 && $check2 && $order->pivot) {
					$total += $order->pivot->quantity;
				}
			}
		}
		return $total;
    }

    public function getOrdersTypeOfPage($adminId, $timeFrom = null, $timeTo = null, Page $page = null)
    {
    	if ($page) {
    		$customers = $customers = Customer::with('orders')->where('user_id', $adminId)->where('fb_page_id', $page->fb_page_id)->get();
    	} else {
    		$customers = Customer::with('orders')->where('user_id', $adminId)->get();
    	}

		$total = 0;
		$newOrder = 0;
		$confirmOrder = 0;
		$sendingOrder = 0;
		$sentOrder = 0;
		$refundingOrder = 0;
		$refundedOder = 0;
		$cancelOrder = 0;
		foreach ($customers as $customer) {
			$orders = $customer->orders;
			foreach ($orders as $order) {
				$timeCreated = strtotime($order->created_at);
    			$check1 = false;
				if ($timeFrom) {
    				$strTimeFrom = strtotime($timeFrom);
					if ($strTimeFrom <= $timeCreated) {
						$check1 = true;
					}
				} else {
					$check1 = true;
				}

				$check2 = false;
				if ($timeTo) {
    				$strTimeTo = strtotime($timeTo);
    				$strTimeToConfig = strtotime('+23 hours +59 minutes +59 seconds', $strTimeTo);
	    			if ($timeCreated <= $strTimeToConfig) {
	    				$check2 = true;
	    			}
				} else {
					$check2 = true;
				}

				if ($check1 && $check2) {
					switch ($order->status_order) {
						case 'NEW':
							$newOrder++;
							break;
						case 'CONFIRM':
							$confirmOrder++;
							break;
						case 'SENDING':
							$sendingOrder++;
							break;
						case 'SENT':
							$sentOrder++;
							break;
						case 'REFUNDING':
							$refundingOrder++;
							break;
						case 'REFUNDED':
							$refundedOder++;
							break;
						case 'CANCELED':
							$cancelOrder++;
							break;
						
						default:
							break;
					}
					$total++;
				}
			}
		}
    	return [
    		'newOrder' => $newOrder,
    		'confirmOrder' => $confirmOrder,
    		'sendingOrder' => $sendingOrder,
    		'sentOrder' => $sentOrder,
    		'refundingOrder' => $refundingOrder,
    		'refundedOder' => $refundedOder,
    		'cancelOrder' => $cancelOrder,
    		'total' => $total
    	];
    }

    public function getSaleData(Request $request)
    {
    	$request->validate([
    		'timeFrom' => 'required|date',
    		'timeTo' => 'required|date',
    		'typeMode' => 'required|numeric|in:1,2,3',
    	]);
    	$pageId = $request->pageId;
    	$timeFrom = $request->timeFrom;
    	$timeTo = $request->timeTo;
    	$typeMode = $request->typeMode;

    	$user = Auth::user();
    	$page = $user->pages()->find($pageId);
    	
    	$result = [];
    	$timeFrom = date('Y-m-d', strtotime($timeFrom)) . ' 00:00:00';
    	$timeTo = date('Y-m-d', strtotime($timeTo)) . ' 23:59:59';

    	$strTimeTo = strtotime($timeTo);
		$strTimeFrom = strtotime($timeFrom);

    	if ($page) {
			$items = DB::table('statistic_sales')->select('back_amount', 'discount', 'origin_val', 'profit', 'revenue', 'sale_amount', 'action_time', 'ship_fee')
												->where('user_id', $user->id)
												->where('action_time', '>=', $strTimeFrom)
												->where('action_time', '<=', $strTimeTo)
												->where('page_id', $pageId)
												->get();
    	} else 
    	if ($pageId == "ALL") {
    		$items = DB::table('statistic_sales')->select('back_amount', 'discount', 'origin_val', 'profit', 'revenue', 'sale_amount', 'action_time', 'ship_fee')
    											->where('user_id', $user->id)
												->where('action_time', '>=', $strTimeFrom)
												->where('action_time', '<=', $strTimeTo)
												->whereNotNull('page_id')
												->get();
    	} else 
    	if ($pageId == 'NOTPAGE') {
    		$items = DB::table('statistic_sales')->select('back_amount', 'discount', 'origin_val', 'profit', 'revenue', 'sale_amount', 'action_time', 'ship_fee')
    											->where('user_id', $user->id)
												->where('action_time', '>=', $strTimeFrom)
												->where('action_time', '<=', $strTimeTo)
												->whereNull('page_id')
												->get();
    	} else {
    		$items = DB::table('statistic_sales')->select('back_amount', 'discount', 'origin_val', 'profit', 'revenue', 'sale_amount', 'action_time', 'ship_fee')
    											->where('user_id', $user->id)
												->where('action_time', '>=', $strTimeFrom)
												->where('action_time', '<=', $strTimeTo)
												->get();
    	}

		$strTimeInd = $strTimeFrom;
		$index = 0;

		while ($strTimeInd <= $strTimeTo) {
			$result[$index]['saleAmount'] = 0;
			$result[$index]['backAmount'] = 0;
			$result[$index]['discount'] = 0;
			$result[$index]['revenue'] = 0;
			$result[$index]['originVal'] = 0;
			$result[$index]['profit'] = 0;
			$result[$index]['shipFee'] = 0;
			switch ($typeMode) {
				case 1:
					$strTimeNextInd = strtotime('+1 day', $strTimeInd);
					break;
				case 2:
					$strTimeNextInd = strtotime('+1 month', $strTimeInd);
					break;
				case 3:
					$strTimeNextInd = strtotime('+1 year', $strTimeInd);
					break;
				
				default:
					$strTimeNextInd = strTimeTo('+1 year', $strTimeTo);
					break;
			}
			foreach ($items as $item) {
    			$timeItem = $item->action_time;
    			if ($strTimeInd <= $timeItem && $timeItem < $strTimeNextInd) {
    				$result[$index]['saleAmount'] += $item->sale_amount;
    				$result[$index]['backAmount'] += $item->back_amount;
    				$result[$index]['discount'] += $item->discount;
    				$result[$index]['revenue'] += $item->revenue;
    				$result[$index]['originVal'] += $item->origin_val;
    				$result[$index]['profit'] += $item->profit;
    				$result[$index]['shipFee'] += $item->ship_fee;
    			}
			}
			$strTimeInd = $strTimeNextInd;
			$index++;
			if ($index > 62) {
				return response()->json([
					'message' => 'Khoảng thời gian quá rộng'
				], 302);
			}
		}
    	return response()->json($result);
    }

    public function getTop10Post(Request $request)
    {
    	$pageId = $request->pageId;

    	$user = Auth::user();
        $adminId = $user->adminId();
    	$page = $user->pages()->find($pageId);

    	if ($page) {
    		$posts = PostPage::where('user_id', $adminId)
							->where('fb_page_id', $page->fb_page_id)
                            ->take(100)
							->get();
            foreach ($posts as $k => $post) {
                $condict = [
                    ['user_id', $adminId],
                    ['post_id', $post->id]
                ];
                $posts[$k]->comments_count = (new CommentRespository($adminId))->getCount($condict);
            }
            $posts = $this->sortPostFollowComments($posts);
            $posts = $this->takeItemFromArray($posts, 10);
    	} else {
    		$posts = PostPage::where('user_id', $adminId)
                            ->take(100)
							->get();
            foreach ($posts as $k => $post) {
                $condict = [
                    ['user_id', $adminId],
                    ['post_id', $post->id]
                ];
                $posts[$k]->comments_count = (new CommentRespository($adminId))->getCount($condict);
            }
            $posts = $this->sortPostFollowComments($posts);
            $posts = $this->takeItemFromArray($posts, 10);
    	}

    	return response()->json($posts);
    }

    /**
     * take limit item from array
     * @param $arr array input
     * @param  [type] $num [item quantity]
     * @return [type]    [array item with limit]
     */
    public function takeItemFromArray($arr, $num)
    {
        $result = [];
        foreach ($arr as $key => $value) {
            if ($key > $num - 1) {
                break;
            }
            array_push($result, $value);
        }
        return $result;
    }

    public function sortPostFollowComments($posts)
    {
        for ($i=0; $i < count($posts) - 1; $i++) { 
            for ($j=$i + 1; $j < count($posts); $j++) { 
                if ($posts[$i]->comments_count < $posts[$j]->comments_count) {
                    $tg = $posts[$i];
                    $posts[$i] = $posts[$j];
                    $posts[$j] = $tg;
                }
            }
        }
        return $posts;
    }

    public function getTop10Products()
    {
    	$user = Auth::user();
    	$products = $user->productUsers()
    					->select('prod_code', 'prod_name', 'prod_price', 'prod_quantity', 'prod_thumb', 'count_childs', 'properties', 'count_sold')
    					->withCount('orders')
    					->orderBy('count_sold', 'desc')
    					->take(10)
    					->get();
    	return response()->json($products);
    }

    public function getConversationData(Request $request)
    {
    	$request->validate([
    		'typeMode' => 'required|numeric|in:1,2,3',
    		'timeFrom' => 'required|date',
    		'timeTo' => 'required|date',
    	]);
    	$pageId = intval($request->pageId);
    	$timeFrom = $request->timeFrom;
    	$timeTo = $request->timeTo;
    	$typeMode = $request->typeMode;
    	$user = Auth::user();
        $adminId = $user->adminId();
    	$page = $user->pages()->find($pageId);

    	$result = [];
    	$timeFrom = date('Y-m-d', strtotime($timeFrom)) . ' 00:00:00';
    	$timeTo = date('Y-m-d', strtotime($timeTo)) . ' 23:59:59';

    	$strTimeTo = strtotime($timeTo);
		$strTimeFrom = strtotime($timeFrom);

    	if ($page) {
            $condict = [
                ['user_id', $adminId],
                ['fb_page_id', $page->fb_page_id],
                ['created_time', '>=', $strTimeFrom],
                ['created_time', '<=', $strTimeTo]
            ];
    		$pagesArr = [$page->fb_page_id];
    	} else {
            $condict = [
                ['user_id', $adminId],
                ['created_time', '>=', $strTimeFrom],
                ['created_time', '<=', $strTimeTo]
            ];
    		$pages = $user->pages()->get();
    		$pagesArr = [];
    		foreach ($pages as $page) {
    			if ($page->active) {
    				array_push($pagesArr, $page->fb_page_id);
    			}
    		}
    	}
        $comments = (new CommentRespository($adminId))->getCondiction($condict);
        $messages = (new MessageRespository($adminId))->getCondiction($condict);
    	$strTimeInd = $strTimeFrom;
		$index = 0;
    	while ($strTimeInd <= $strTimeTo) {
			$result[$index]['commentsOfPage'] = 0;
			$result[$index]['commentsOfCus'] = 0;
			$result[$index]['messagesOfCus'] = 0;
			$result[$index]['messagesOfPage'] = 0;
			switch ($typeMode) {
				case 1:
					$strTimeNextInd = strtotime('+1 day', $strTimeInd);
					break;
				case 2:
					$strTimeNextInd = strtotime('+1 month', $strTimeInd);
					break;
				case 3:
					$strTimeNextInd = strtotime('+1 year', $strTimeInd);
					break;
				
				default:
					$strTimeNextInd = strTimeTo('+1 year', $strTimeTo);
					break;
			}

			foreach ($comments as $comment) {
    			$timeItem = $comment->created_time;
    			if ($strTimeInd <= $timeItem && $timeItem <= $strTimeNextInd) {
    				if (in_array($comment->from_id, $pagesArr)) {
    					$result[$index]['commentsOfPage']++;
    				} else {
    					$result[$index]['commentsOfCus']++;
    				}
    			}
			}

			foreach ($messages as $message) {
    			$timeItem = $message->created_time;
    			if ($strTimeInd <= $timeItem && $timeItem <= $strTimeNextInd) {
    				if (in_array($message->from_id, $pagesArr)) {
    					$result[$index]['messagesOfPage']++;
    				} else {
    					$result[$index]['messagesOfCus']++;
    				}
    			}
			}

			$strTimeInd = $strTimeNextInd;
			$index++;
			if ($index > 62) {
				return response()->json([
					'message' => 'Khoảng thời gian quá rộng'
				], 302);
			}
		}
		
    	return response()->json($result);
    }

    public function getDataStaff(Request $request)
    {
    	$pageId = $request->pageId;
    	$timeFrom = $request->timeFrom;
    	$timeTo = $request->timeTo;
    	$user = User::find(Auth::id());
        $adminId = $user->adminId();

    	$accounts = $user->accounts()->select('name', 'user_fb_id', 'user_email', 'user_phone', 'id')->get();

    	$timeFrom = date('Y-m-d', strtotime($timeFrom)) . ' 00:00:00';
    	$timeTo = date('Y-m-d', strtotime($timeTo)) . ' 23:59:59';

		$page = $user->pages()->find($pageId);

		$interactive = $this->getReplyOfStaff($adminId, $accounts, $timeFrom, $timeTo, $page);

		$revenue = $this->getRevenueOfStaff($user, $accounts, $timeFrom, $timeTo, $page);

		return response()->json([
			'interactive' => $interactive,
			'revenue' => $revenue
		]);
    }

    public function getRevenueOfStaff(User $user, $accounts, $timeFrom, $timeTo,Page $page = null)
    {
    	if ($page) {
	    	$orders = $user->ordersUser()
	    					->select('total_value', 'user_id', 'status_order', 'page_id', 'value_has_sale')
	    					->where('created_at', '>=', $timeFrom)
	    					->where('created_at', '<=', $timeTo)
	    					->where('page_id', $page->id)
	    					->whereNotIn('status_order', ['NEW', 'REFUNDED', 'REFUNDING', "CANCELED"])
	    					->get();
    	} else {
    		$orders = $user->ordersUser()
	    					->select('total_value', 'user_id', 'status_order', 'page_id', 'value_has_sale')
	    					->where('created_at', '>=', $timeFrom)
	    					->where('created_at', '<=', $timeTo)
	    					->whereNotIn('status_order', ['NEW', 'REFUNDED', 'REFUNDING', "CANCELED"])
	    					->get();
    	}

    	$total = 0;
    	$staffsOf = 0;
		$flat = true;
		$index = 0;
		$staffs = [];
		foreach ($accounts as $account) {
			$staffs[$index]['staff'] = $account;
			$staffs[$index]['revenue'] = 0;
    		foreach ($orders as $order) {
    			$revenue = $order->value_has_sale - $order->discount;
    			if ($order->user_id == $account->id) {
    				$staffs[$index]['revenue'] += $revenue;
    				$staffsOf += $revenue;
    			}
    			if ($flat) {
    				$total += $revenue;
    			}
    		}
    		$flat = false;
    		$index++;
		}
		$pageOf = $total - $staffsOf;

		return [
			'total' => $total,
			'staffsOf' => $staffsOf,
			'pageOf' => $pageOf,
			'staffs' => $staffs
		];
    }

    public function getReplyOfStaff($adminId, $accounts, $timeFrom, $timeTo, Page $page = null)
    {
        $timeFrom = strtotime($timeFrom);
        $timeTo = strtotime($timeTo);
    	if ($page) {
            $condict = [
                ['user_id', $adminId],
                ['fb_page_id', $page->fb_page_id],
                ['created_time', '>=', $timeFrom],
                ['created_time', '<=', $timeTo]
            ];
    	} else {
            $condict = [
                ['user_id', $adminId],
                ['created_time', '>=', $timeFrom],
                ['created_time', '<=', $timeTo]
            ];
    	}
        $comments = (new CommentRespository($adminId))->getCondiction($condict);
        $messages = (new MessageRespository($adminId))->getCondiction($condict);
    	$total = count($comments) + count($messages);
    	$totalStaff = 0;
    	$staffs = [];
    	$index = 0;
    	foreach ($accounts as $account) {
    		$staffs[$index]['comments_count'] = 0;
    		$staffs[$index]['messages_count'] = 0;
			$staffs[$index]['staff'] = $account;
    		foreach ($comments as $comment) {
    			if ($comment->staff_reply_id == $account->id) {
    				$staffs[$index]['comments_count']++;
    				$totalStaff++;
    			}
    		}
    		foreach ($messages as $message) {
    			if ($message->staff_reply_id == $account->id) {
    				$staffs[$index]['messages_count']++;
    				$totalStaff++;
    			}
    		}
    		$index++;
    	}

    	return [
    		'total' => $total,
    		'staffsOf' => $totalStaff,
    		'pageOf' => $total - $totalStaff,
    		'staffs' => $staffs
    	];
    }
}
