<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralSettingRequest;
use App\Http\Requests\BlackCustomerRequest;
use App\Http\Requests\UpdateReportsRequest;
use App\Http\Requests\Api\User\UpdateAutoSetting;
use App\models\SettingBasic;
use Auth;
use App\User;
use App\models\GroupCustomer;
use App\models\CustomerAndGroup;
use App\models\QuickAnswer;
use App\models\UserQuickAnswer;
use App\models\CustomerReport;
use App\models\InfoMarketing;

class SettingController extends Controller
{
    public function updateGeneralSetting(GeneralSettingRequest $request)
    {
        $user = User::find(Auth::id());
        $id = $user->generalSetting()
                    ->update([
                        'hide_all_cmt' => json_decode($request->hide_all_cmt),
                        'hide_cmt_has_phone' => json_decode($request->hide_cmt_has_phone),
                        'hide_cmt_has_key' => json_decode($request->hide_cmt_has_key),
                        'key_cmt_hide' => base64_encode(json_encode($request->key_cmt_hide)),
                        'auto_like' => json_decode($request->auto_like),
                        'ping_notify' => json_decode($request->ping_notify),
                        'priority_cmt_has_key' => json_decode($request->priority_cmt_has_key),
                        'key_cmt_priority' => base64_encode(json_encode($request->key_cmt_priority)),
                        'filter_email' => json_decode($request->filter_email),
                        'filter_phone' => json_decode($request->filter_phone)
                    ]);
        $setting = $user->generalSetting()->first();
    	$setting->key_cmt_hide  = json_decode(base64_decode($setting->key_cmt_hide));
    	$setting->key_cmt_priority  = json_decode(base64_decode($setting->key_cmt_priority));
    	return response()->json([
    		'updated' => true,
    		'data' => $setting
    	]);
    }

    public static function createGeneralSetting(User $user)
    {
        $setting = SettingBasic::create([
            'user_id' => $user->adminId(),
            'staff_id' => $user->id,
            'key_cmt_hide' => base64_encode(json_encode([])),
            'key_cmt_priority' => base64_encode(json_encode([])),
            'filter_email' => true,
            'filter_phone' => true,
        ]);
        return $setting;
    }

    public function getGeneralSetting()
    {
    	$setting = Auth::user()->generalSetting()->first();
    	if ($setting) {
	    	$setting->key_cmt_hide  = json_decode(base64_decode($setting->key_cmt_hide));
	    	$setting->key_cmt_priority  = json_decode(base64_decode($setting->key_cmt_priority));
	    	return response()->json($setting);
    	}
    	return null;
    }

    /**
     * get all group of user and group default
     * @return [json] 
     */
    public function getGroups() 
    {
        return Auth::user()->groupsCustomer()->get()->toJson();
    }

    public function createGroups(Request $request)
    {
        $request->validate(
            [
                'group_name' => 'required|max:190',
                'group_color' => 'required|max:190'
            ],
            [
                'group_name.required' => 'Vui lòng nhập tên khách hàng',
                'group_name.max' => 'Tên nhóm khách hàng quá dài',
                'group_color.required' => 'Vui lòng chọn màu cho nhóm'
            ]
        );

        $user = User::find(Auth::id());
        $group = GroupCustomer::create([
            'user_id' => $user->adminId(),
            'staff_id' => $user->id,
            'group_name' => $request->group_name,
            'group_color' => $request->group_color
        ]);

        return response()->json([
            'group' => $group
        ]);
    }

    public function destroyGroup($id)
    {
        $group = Auth::user()->groupsCustomer()->find($id);
        if ($group) {
            $group->delete();
            return response()->json([
                'deleted' => true
            ]);
        }
        return response()->json([
            'message' => 'không tìm thấy đối tượng'
        ], 302);
    }

    public function updateGroup($id, Request $request)
    {
        $request->validate(
            [
                'group_name' => 'required|max:190',
                'group_color' => 'required|max:190'
            ],
            [
                'group_name.required' => 'Vui lòng nhập tên khách hàng',
                'group_name.max' => 'Tên nhóm khách hàng quá dài',
                'group_color.required' => 'Vui lòng chọn màu cho nhóm'
            ]
        );

        $update = Auth::user()->groupsCustomer()
                                ->find($id)
                                ->update([
                                    'group_name' => $request->group_name,
                                    'group_color' => $request->group_color
                                ]);
        if ($update) {
            return response()->json([
                'updated' => true
            ]);
        }   

        return response()->json([
            'message' => 'Không tồn tại đối tượng'
        ], 302);
    }

    public function getQuickAnswer(Request $request) 
    {
        $request->validate([
            'keyword' => 'nullable'
        ]);

        $keyword = $request->keyword;

        $quickAnswers = Auth::user()->quickAnswers()
                                    ->where('quick_text', 'like', "%$keyword%")
                                    ->get();

        return response()->json($quickAnswers);
    }

    public function createQuickAnswer(Request $request)
    {
        $request->validate(
            [
                'quick_text' => 'required|max:190',
                'answer_text' => 'required|max:500'
            ],
            [
                'quick_text.required' => 'Vui lòng nhập từ viết tắt',
                'answer_text.required' => 'Vui lòng nhập nội dung câu trả lời',
                'quick_text.max' => 'Từ viết tắt quá dài',
                'answer_text.max' => 'Nội dung câu trả lời quá dài',
            ]
        );
        $user = Auth::user();
        $answer = QuickAnswer::create([
            'user_id' => $user->adminId(),
            'staff_id' => $user->id,
            'quick_text' => $request->quick_text,
            'answer_text' => $request->answer_text
        ]);

        return response()->json([
            'answer' => $answer
        ]);
    }

    public function destroyAnswer($id)
    {
        $answer = Auth::user()->quickAnswers()
                                ->find($id);

        if ($answer) {

            optional(QuickAnswer::find($id))->delete();

            return response()->json([
                'deleted' => true
            ]);
        }

        return response()->json([
            'message' => 'Không thể xóa câu trả lời mẫu này'
        ], 302);
    }

    public function updateQuickAnswer($id, Request $request)
    {
        $request->validate(
            [
                'quick_text' => 'required|max:190',
                'answer_text' => 'required|max:500'
            ],
            [
                'quick_text.required' => 'Vui lòng nhập từ viết tắt',
                'answer_text.required' => 'Vui lòng nhập nội dung câu trả lời',
                'quick_text.max' => 'Từ viết tắt quá dài',
                'answer_text.max' => 'Nội dung câu trả lời quá dài',
            ]
        );

        $update = Auth::user()->quickAnswers()
                                ->find($id)
                                ->update([
                                    'quick_text' => $request->quick_text,
                                    'answer_text' => $request->answer_text
                                ]);

        if ($update) {
            return response()->json([
                'updated' => true
            ]);
        }

        return response()->json([
            'message' => 'Không thể cập nhật'
        ], 302);
    }

    public function createCustomerBlack(BlackCustomerRequest $request)
    {
        $user = Auth::user();
        $cus = CustomerReport::create([
            'id_user_reported' => $user->id,
            'user_id' => $user->adminId(),
            'title_report' => $request->title_report,
            'des_report' => $request->des_report,
            'name_cus' => $request->name_cus,
            'phone_cus' => $request->phone_cus,
            'email_cus' => $request->email_cus,
            'fb_id_cus' => $request->fb_id_cus
        ]);

        return response()->json([
            'created' => true,
            'data' => $cus
        ]);
    }

    public function getReportsCustomer(Request $request)
    {
        $request->validate(
            [
                'fb_id_cus' => 'required|numeric|min:1',
            ]
        );
        $phone = $request->phone_cus;
        $fbId = $request->fb_id_cus;
        if ($phone) {
            $reports = CustomerReport::where('fb_id_cus', $fbId)
                        ->orWhere('phone_cus', $phone)
                        ->get();
        } else {
            $reports = CustomerReport::where('fb_id_cus', $fbId)->get();
        }
        return response()->json([
            'reports' => $reports
        ]);
    }

    public function getBlackList()
    {
        $userId = Auth::id();
        return Auth::user()->blackList()
                            ->where('id_user_reported', $userId)
                            ->paginate(10)->toJson();
    }

    public function updateReports(UpdateReportsRequest $request)
    {
        $reports = $request->reports;
        foreach ($reports as $report) {
            CustomerReport::find($report['id'])->update([
                'name_cus' => $report['name_cus'],
                'phone_cus' => $report['phone_cus'],
                'email_cus' => $report['email_cus'],
                'title_report' => $report['title_report'],
                'des_report' => $report['des_report'],
            ]);
        }
        return response()->json([
            'updated' => true
        ]);
    } 

    public function destroyReport($id)
    {
        $report = Auth::user()->blackList()
                    ->find($id);
        if ($report) {
            $report->delete();
            return response()->json([
                'deleted' => true
            ]);
        }
        return response()->json([
            'message' => 'Không thể xóa báo xấu này'
        ], 302);
    }

    public function getListPages()
    {
        return Auth::user()->pages()->get()->toJson();
    } 

    public function getInfoMarketting()
    {
        $userId = Auth::id();
        $setting = Auth::user()->generalSetting()->first();
        if ($setting) {
            $setting->key_cmt_hide  = json_decode(base64_decode($setting->key_cmt_hide));
            $setting->key_cmt_priority  = json_decode(base64_decode($setting->key_cmt_priority));
        }
        $count_email = InfoMarketing::where('user_id', $userId)
                                    ->where('email_mar', '<>', null)
                                    ->count();
        $count_phone = InfoMarketing::where('user_id', $userId)
                                    ->where('phone_mar', '<>', null)
                                    ->count();
        return response()->json([
            'setting' => $setting,
            'count_email' => $count_email,
            'count_phone' => $count_phone
        ]);
    }

    public function exportEmailMarketting($type)
    {
        $userId = Auth::id();
        $emails = InfoMarketing::select('email_mar')   
                                ->where('user_id', $userId)
                                ->whereNotNull('email_mar')
                                ->get()
                                ->toArray();

        return \Excel::create('email_marketting', function($excel) use ($emails) {
            $excel->sheet('email', function($sheet) use ($emails)
            {
                $sheet->fromArray($emails);
            });
        })->download($type);
    }  

    public function exportPhoneMarketting($type)
    {
        $userId = Auth::id();
        $emails = InfoMarketing::select('phone_mar')   
                                ->where('user_id', $userId)
                                ->whereNotNull('phone_mar')
                                ->get()
                                ->toArray();
        return \Excel::create('phone_marketting', function($excel) use ($emails) {
            $excel->sheet('phone_number', function($sheet) use ($emails)
            {
                $sheet->fromArray($emails);
            });
        })->download($type);
    }    

    public function getAutoSetting()
    {
        $user = Auth::user();
        $setting = SettingBasic::where('user_id', $user->adminId())->first();
        $setting = $this->decodeSetting($setting);
        return response()->json($setting);
    }

    public function updateAutoSetting(UpdateAutoSetting $request)
    {
        $auto_comment = $request->auto_comment;
        $auto_inbox = $request->auto_inbox;
        $content_comment = $request->content_comment;
        $content_inbox = $request->content_inbox;
        $has_time_inbox = $request->has_time_inbox;
        $has_time_comment = $request->has_time_comment;
        $time_start_comment = $request->time_start_comment;
        $time_end_comment = $request->time_end_comment;
        $time_start_inbox = $request->time_start_inbox;
        $time_end_inbox = $request->time_end_inbox;
        $user = Auth::user();
        SettingBasic::where('user_id', $user->adminId())
                        ->update([
                            'content_comment' => $content_comment,
                            'content_inbox' => $content_inbox,
                            'auto_comment' => $auto_comment,
                            'auto_inbox' => $auto_inbox,
                            'has_time_inbox' => $has_time_inbox,
                            'has_time_comment' => $has_time_comment,
                            'time_start_comment' => $time_start_comment,
                            'time_end_comment' => $time_end_comment,
                            'time_start_inbox' => $time_start_inbox,
                            'time_end_inbox' => $time_end_inbox
                        ]);
        return response()->json([
            'success' => true
        ]);
    }

    public function decodeSetting(SettingBasic $setting)
    {
        $setting->key_cmt_hide = json_decode(base64_decode($setting->key_cmt_hide));
        $setting->key_cmt_priority = json_decode(base64_decode($setting->key_cmt_priority));
        return $setting;
    }
}
