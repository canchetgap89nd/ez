<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\models\Role;
use App\models\Order;
use App\models\Product;
use App\models\Customer;
use App\Respositories\Conversation\ConverRespository;
use App\Respositories\Conversation\CommentRespository;
use App\Respositories\Conversation\MessageRespository;
use App\models\Mongo\ActivityUser;
use Carbon\Carbon;
use App\Http\Controllers\Api\ExtratorFacebook\ConvertUidPhoneController;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
    	$keyword = $request->keyword;
    	$role = $request->role;
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
    	$roles = Role::all();
    	$queryUser = User::select('name', 'user_phone', 'user_email', 'user_fb_id', 'id', 'blocked', 'user_time_expire', 'destroyed', 'time_expire_blocked', 'parent_user_id', 'created_at')
    					->withCount(['pages as pagesActive' => function($query) {
    						$query->where('active', 1);
    					}, 'pages as pagesTotal', 'accounts'])
    					->with('roles');
    	if ($keyword || $role || $timeFrom) {
    		$customUrl = '';
            if ($keyword) {
                $checkCount = User::where('id', $keyword)->count();
	    		if ($checkCount) {
	    			$queryUser = $queryUser->where('id', $keyword);
	    		} else {
	    			$checkCount = User::where('user_email', $keyword)->count();
	    			if ($checkCount) {
	    				$queryUser = $queryUser->where('user_email', $keyword);
	    			} else {
	    				$checkCount = User::where('user_phone', $keyword)->count();
	    				if ($checkCount) {
	    					$queryUser = $queryUser->where('user_phone', $keyword);
	    				} else {
	    					$queryUser = $queryUser->where('name', 'like', "%$keyword%");
	    				}
	    			}
	    		}
            }

            if ($role) {
                if ($keyword) {
                    $customUrl .= '&role=' . $role;
                } else {
                    $customUrl .= '?role=' . $role;
                }
                $queryUser = $queryUser->whereHas('roles', function($query) use ($role) {
                	 $query->where('roles.id', $role);
                });
            }

            if ($timeFrom && $timeTo) {
                if ($keyword || $role) {
                    $customUrl .= '&timeFrom=' . $timeFrom . '&timeTo=' . $timeTo;
                } else {
                    $customUrl .= '?timeFrom=' . $timeFrom . '&timeTo=' . $timeTo;
                }
                $queryUser = $queryUser->where('created_at', '>=', $timeFrom)
                                		->where('created_at', '<=', $timeTo);
            }
            $accounts = $queryUser->orderBy('created_at', 'desc')->paginate(10)->withPath($customUrl);
    	} else {
    		$accounts = $queryUser->orderBy('created_at', 'desc')->paginate(10);
        }
        
        foreach ($accounts as $item) {
            $adminId = $item->adminId();
            $condict = [
                ['user_id', $adminId]
            ];
            $item->count_conversations = (new ConverRespository($adminId))->getCount($condict);
        }

        if ($timeFrom && $timeTo) {
            $usersInRanger = User::whereBetween('created_at', [
                                            $timeFrom,
                                            $timeTo
                                        ])
                                        ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                                        ->groupBy('date')
                                        ->get();
            $usersInRanger = $this->fixDayNotHasData($usersInRanger, $timeFrom, $timeTo);
        } else {
            $timeF = Carbon::now()->subDays(30);
            $timeT = Carbon::now();
            $usersInRanger = User::whereBetween('created_at', [
                                        $timeF,
                                        $timeT
                                    ])
                                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                                    ->groupBy('date')
                                    ->get();
            $usersInRanger = $this->fixDayNotHasData($usersInRanger, $timeF, $timeT);
        }
    	return view('admin.accounts', compact('accounts', 'keyword', 'role', 'timeFrom', 'timeTo', 'roles', 'usersInRanger'));
    }

    public function getDetailUser($id, Request $request)
    {
    	$user = User::with(['accounts' => function($query) {
    		$query->with('roles');
    	}, 'pages', 'parent'])
    				->find($id);
        $adminId = $user->adminId();
    	$timeFrom = $request->timeFrom;
    	$timeTo = $request->timeTo;
    	$activities = ActivityUser::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
    	$countSuccessOrders = Order::where('user_id', $adminId);
		$countOrders = Order::where('user_id', $adminId);
		$countProducts = Product::where('user_id', $adminId);
		$countConversations = (new ConverRespository($adminId))->getDB()->where('user_id', $adminId);
		$countComments = (new CommentRespository($adminId))->getDB()->where('user_id', $adminId);
		$countMessages = (new MessageRespository($adminId))->getDB()->where('user_id', $adminId);
		$countCustomers = Customer::where('user_id', $adminId);
    	if ($timeFrom) {
    		$timeFrom = Carbon::createFromTimestamp(strtotime($timeFrom));
			$timeTo = Carbon::createFromTimestamp(strtotime($timeTo));
	    	$countSuccessOrders = $countSuccessOrders->whereBetween('created_at', [
				    									$timeFrom,
				    									$timeTo
				    								])
							    					->whereNotNull('time_sent')
							    					->count();
			$countOrders = $countOrders->whereBetween('created_at', [
	    									$timeFrom,
	    									$timeTo
	    								])
				    					->count();
			$countProducts = $countProducts->whereBetween('created_at', [
												$timeFrom,
												$timeTo
											])
											->count();
			$countConversations = $countConversations->where('created_at', '>=', $timeFrom)
														->where('created_at', '<=', $timeTo)
														->count();
			$countComments = $countComments->where('created_at', '>=', $timeFrom)
											->where('created_at', '<=', $timeTo)
											->count();
			$countMessages = $countMessages->where('created_at', '>=', $timeFrom)
											->where('created_at', '<=', $timeTo)
											->count();
			$countCustomers = $countCustomers->whereBetween('created_at', [
												$timeFrom,
												$timeTo
											])
											->count();
    	} else {
			$countSuccessOrders = $countSuccessOrders->count();
			$countOrders = $countOrders->count();
			$countProducts = $countProducts->count();
			$countConversations = $countConversations->count();
			$countComments = $countComments->count();
			$countMessages = $countMessages->count();
			$countCustomers = $countCustomers->count();
    	}
    	return view('admin.detail_account', compact('user', 'activities', 'countSuccessOrders', 'countOrders', 'countProducts', 'countConversations', 'countComments', 'countMessages', 'countCustomers', 'timeFrom', 'timeTo'));
    }

    public function getActivities($id)
    {
    	$user = User::find($id);
    	$activities = ActivityUser::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
    	return view('admin.activities_account', compact('user', 'activities'));
    }

    public function fixDayNotHasData($data, $timeFrom, $timeTo)
    {
        $data = json_encode($data);
        $data = json_decode($data, true);
        $timeFrom = strtotime($timeFrom);
        $timeFrom = date('Y-m-d', $timeFrom);
        $fday = strtotime($timeFrom);
        $tday = strtotime($timeFrom);
        $day = $fday;
        $result = [];
        $date = Carbon::parse($timeFrom);
        $now = Carbon::parse($timeTo);
        $diff = $date->diffInDays($now);
        for ($i=1; $i <= $diff + 1; $i++) {
            $hasThis = false;
            foreach ($data as $item) {
                if ($day === strtotime($item['date'])) {
                    $hasThis = true;
                    break;
                }
            }
            if ($hasThis) {
                array_push($result, $item);
            } else {
                $tg['date'] = date('Y-m-d', $day);
                $tg['total'] = 0;
                array_push($result, $tg);
            }
            $day = strtotime('+1 days', $day);
        }
        return $result;
    }

    public function getActivityEveryDays(Request $request)
    {
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        if ($timeFrom) {
            $timeF = new \MongoDB\BSON\UTCDateTime(strtotime($timeFrom)*1000);
            $timeT = new \MongoDB\BSON\UTCDateTime(strtotime($timeTo)*1000);
            $timeFrom = Carbon::createFromTimestamp(strtotime($timeFrom));
            $timeTo = Carbon::createFromTimestamp(strtotime($timeTo));
            $activities = ActivityUser::where('created_at', '>=', $timeFrom)
                                        ->where('created_at', '<=', $timeTo)
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);
            $activitiesRanger = ActivityUser::raw(function($collection) use ($timeF, $timeT) {
                return $collection->aggregate([
                            [
                                '$match' => [
                                    'created_at' => [
                                        '$gte' => $timeF,
                                        '$lte' => $timeT
                                    ]
                                ],

                            ],
                            [
                                '$group' => [
                                    '_id' => [
                                        '$dateToString' => [
                                            'format' => '%Y-%m-%d',
                                            'date' => [
                                                '$toDate' => '$created_at'
                                            ],
                                            'timezone' => 'Asia/Saigon'
                                        ]
                                    ],
                                    'total' => [
                                        '$sum' => 1
                                    ]
                                ]
                            ]
                        ]);
            });
            $activitiesRanger = $this->fixDayAndConvertTime($activitiesRanger, $timeFrom, $timeTo);
        } else {
            $activities = ActivityUser::orderBy('created_at', 'desc')->paginate(10);
            $activitiesRanger = [];
        }
       
        return view('admin.activity_every_day', compact('activities', 'timeFrom', 'timeTo', 'activitiesRanger'));
    }

    /**
     * convert name column _id to date and fix total = 0 with day not has data
     * @param  [type]  $data   [data collection]
     * @param  [type]  $numDay [number day need get]
     * @param  integer $began  [day begin, format date]
     * @return [type]          [collection array]
     */
    public function fixDayAndConvertTime($data, $timeFrom, $timeTo)
    {
        $data = json_encode($data);
        $data = json_decode($data, true);
        $timeFrom = strtotime($timeFrom);
        $timeFrom = date('Y-m-d', $timeFrom);
        $fday = strtotime($timeFrom);
        $tday = strtotime($timeFrom);
        $day = $fday;
        $result = [];
        $date = Carbon::parse($timeFrom);
        $now = Carbon::parse($timeTo);
        $diff = $date->diffInDays($now);
        for ($i=1; $i <= $diff + 1; $i++) {
            $hasThis = false;
            foreach ($data as $item) {
                if ($day === strtotime($item['_id'])) {
                    $hasThis = true;
                    break;
                }
            }
            if ($hasThis) {
                $tg['date'] = date('Y-m-d', $day);
                $tg['total'] = $item['total'];
                array_push($result, $tg);
            } else {
                $tg['date'] = date('Y-m-d', $day);
                $tg['total'] = 0;
                array_push($result, $tg);
            }
            $day = strtotime('+1 days', $day);
        }
        return $result;
    }

    public function getLinkFace($psid)
    {
        $info = (new ConvertUidPhoneController)->getInfoFacebookFromPSID($psid);
        $arr = json_decode($info, true);
        if (isset($arr['username'])) {
            $username = $arr['username'];
            $newURL = 'https://www.facebook.com/' . $username;
            header('Location: '.$newURL);
            exit;
        }
        echo "Không tìm thấy Facebook";
    }
}
