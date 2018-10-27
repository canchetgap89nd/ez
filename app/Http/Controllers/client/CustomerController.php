<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\InfoCustomerRequest;
use App\Http\Requests\QuickAddNoteRequest;
use App\models\CustomerNote;
use App\models\Customer;
use App\models\Page;
use App\models\GroupCustomer;
use App\models\CustomerAndGroup;
use App\models\CustomerReport;
use App\User;
use App\Respositories\Conversation\ConverRespository;

class CustomerController extends Controller
{
    public function hasCustomer($cusId = null, $fbId = null, $phone = null)
    {
        $user = Auth::user();
        if ($cusId !== null) {
            $cus = $user->customersUser()->find($cusId);
            return $cus;
        }

        if ($fbId !== null) {
            $cus = $user->customersUser()->where('fb_id_cus',$fbId)->first();
            return $cus;
        }

        if ($phone !== null) {
            $cus = $user->customersUser()->where('phone_cus', $phone)->first();
            return $cus;
        }

        return false;
    }

    public function getListCustomer(Request $request)
    {
        $sortBy = $request->sortBy;
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $groupBy = $request->groupBy;
        $keyword = $request->keyword;
        $hasMail = json_decode($request->hasMail);
        $hasPhone = json_decode($request->hasPhone);
        $hasOrder = json_decode($request->hasOrder);
        $user = Auth::user();
        $adminId = $user->adminId();
        $arrWhere = [
            ['name_cus', 'like', "%$keyword%"]
        ];

        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['updated_at', '>=', $timeFrom]);
        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['updated_at', '<=', $timeTo]);
        }
        if ($hasMail) {
            array_push($arrWhere, ['email_cus', '<>', null]);
        }
        if ($hasPhone) {
            array_push($arrWhere, ['phone_cus', '<>', null]);
        }
        $arrCusIds = [];
        $arrCusIds1 = [];
        if ($groupBy) {
            $customerHasGr = CustomerAndGroup::select('customer_id')->where('group_id', $groupBy)->get()->toArray();
            foreach ($customerHasGr as $customer) {
                array_push($arrCusIds1, $customer['customer_id']);
            }
            if (!$hasOrder) {
                $arrCusIds = $arrCusIds1;
            }
        }
        $arrCusIds2 = [];
        if ($hasOrder) {
            $orders = $user->ordersUser()->select('cus_id')->get();
            foreach ($orders as $order) {
                array_push($arrCusIds2, $order['cus_id']);
            }
            if ($groupBy) {
                $arrCusIds = array_intersect($arrCusIds1, $arrCusIds2);
            } else {
                $arrCusIds = $arrCusIds2;
            }
        }

        if ($groupBy || $hasOrder) {
            $customers = $user->customersUser()
                                ->withCount(['orders'])
                                ->with('payments')
                                ->where($arrWhere)
                                ->whereIn('id', $arrCusIds)
                                ->orderBy('name_cus', $sortBy)
                                ->paginate(10);
            $arrIds = groupToArray($customers);
            foreach ($customers as $key => $customer) {
                $customers[$key]->comments_count = (new ConverRespository($adminId))->getCount([
                                                                                                ['user_id', $adminId],
                                                                                                ['from_id', $customer->fb_id_cus]
                                                                                            ]);
            }
            return response()->json($customers);
        }

        $customers = $user->customersUser()
                            ->withCount(['orders'])
                            ->with('payments')
                            ->where($arrWhere)
                            ->orderBy('name_cus', $sortBy)
                            ->paginate(10);
        foreach ($customers as $key => $customer) {
            $customers[$key]->comments_count = (new ConverRespository($adminId))->getCount([
                                                                                    ['user_id', $adminId],
                                                                                    ['from_id', $customer->fb_id_cus]
                                                                                ]);
        }
        return response()->json($customers);
    }

    public function searchCustomer(Request $request)
    {
        $phone = $request->phone;
        $user = Auth::user();
        $customers = $user->customersUser()->where('phone_cus', 'like', "%$phone%")->get()->toArray();
        $arrPhone = [];
        $arrMail = [];
        $arrFb = [];
        foreach ($customers as $customer) {
            if ($customer['phone_cus']) {
                array_push($arrPhone, $customer['phone_cus']);
            }
            if ($customer['email_cus']) {
                array_push($arrMail, $customer['email_cus']);
            }
            if ($customer['fb_id_cus']) {
                array_push($arrFb, $customer['fb_id_cus']);
            }
        }
        $reports = CustomerReport::whereIn('phone_cus', $arrPhone)
                        ->orWhereIn('email_cus', $arrMail)
                        ->orWhereIn('fb_id_cus', $arrFb)
                        ->get()
                        ->toArray();
        foreach ($customers as $key => $customer) {
            $customers[$key]['reports'] = [];
            foreach ($reports as $report) {
                if ($customer['phone_cus'] == $report['phone_cus'] || $customer['email_cus'] == $report['email_cus'] || $customer['fb_id_cus'] == $report['fb_id_cus']) {
                    $customers[$key]['reports'][] = $report;
                }
            }
        }

        return response()->json($customers);
    }   

    public function getAddCustomer()
    {
        $groups = Auth::user()->groupsCustomer()->get();
        return response()->json([
            'groups' => $groups
        ]);
    }

    public function addCustomer(CreateCustomerRequest $request)
    {
        $user = User::find(Auth::id());
        $groups = $request->groups;
        $notes = $request->notes;
        $phone = $request->phone_cus;

        $customerHas = $this->hasCustomer(null, null, $phone);

        if (!$customerHas) {

            $newCus = Customer::create([
                'user_id' => $user->adminId(),
                'staff_id' => $user->id,
                'name_cus' => $request->name_cus,
                'phone_cus' => $request->phone_cus,
                'email_cus' => $request->email_cus,
                'address_cus' => $request->address_cus
            ]);

            $arr = [];
            foreach ($groups as $group) {
                $gr = GroupCustomer::find($group['id']);
                if ($gr) {
                    array_push($arr, $gr->id);
                }
            }
            $newCus->groups()->attach($arr);

            foreach ($notes as $note) {
                $this->createNoteCustomer($newCus->id, $note['note_content']);
            }

            return response()->json([
                'created' => true
            ]);

        }

        return response()->json([
            'message' => 'Thông tin khách hàng này đã tồn tại',
            'data' => $customerHas
        ], 302);  
    }

    public function getInfoCustomer($customerId)
    {
        $user = Auth::user();
        $adminId = $user->adminId();
        $customer = $user->customersUser()
                        ->with(['notes' => function($query) {
                            $query->orderBy('created_at', 'desc');
                        }, 'groups', 'pages', 'payments'])
                        ->withCount('orders')
                        ->find($customerId);
        if ($customer) {
            $customer->comments_count = (new ConverRespository($adminId))
                                            ->getCount([
                                                ['user_id', $adminId],
                                                ['from_id', $customer->fb_id_cus],
                                            ]);
        }
        return response()->json($customer);
    }

    public function getInfoCustomerWithIdFb($fbId)
    {
        $user = Auth::user();
        $adminId = $user->adminId();
        $customer = $user->customersUser()
                        ->with(['notes' => function($query) {
                            $query->orderBy('created_at', 'desc');
                        }, 'groups', 'pages', 'payments'])
                        ->withCount('orders')
                        ->where('fb_id_cus', $fbId)
                        ->first();
        if ($customer) {
            $customer->comments_count = (new ConverRespository($adminId))
                                            ->getCount([
                                                ['user_id', $adminId],
                                                ['from_id', $customer->fb_id_cus],
                                            ]);
            $customer->reports = $customer->reports()->get();
        }
        return response()->json($customer);
    }

    public function createNoteCustomer($customerId, $note)   
    {
        $customerNote = new CustomerNote;
        $customerNote->customer_id = $customerId;
        $customerNote->note_content = $note;
        $customerNote->save();
        return $customerNote->id;
    }

    public function updateNoteCustomer($noteId, $note)
    {
        $noteCus = CustomerNote::find($noteId);
        if ($noteCus) {
            $noteCus->note_content = $note;
            $noteCus->save();
            return $noteCus->id;
        }
        return false;
    }

    public function quickAddCustomer(InfoCustomerRequest $request)
    {
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $cus = $this->hasCustomer($request->id, $request->fb_id_cus, $request->phone_cus);
        $infoCus = null;
        if (!$cus) {
            $page = $user->pages()->where('fb_page_id', $request->fb_page_id)->first();
            if ($page) {
                $newCus = Customer::updateOrCreate(
                    [
                        'user_id' => $adminId,
                        'fb_id_cus' => $request->fb_id_cus,
                        'fb_page_id' => $page->fb_page_id,
                    ],
                    [
                    'staff_id' => $user->id,
                    'name_cus' => $request->name_cus,
                    'phone_cus' => $request->phone_cus,
                    'email_cus' => $request->email_cus,
                    'address_cus' => $request->address_cus
                    ]
                );
                $infoCus = Customer::with('notes', 'groups')->find($newCus->id);
            }
        } else {
            $cus->update([
                'fb_id_cus' => $request->fb_id_cus,
                'name_cus' => $request->name_cus,
                'phone_cus' => $request->phone_cus,
                'email_cus' => $request->email_cus,
                'address_cus' => $request->address_cus
            ]);
            $infoCus = Customer::with('notes', 'groups')->find($cus->id);
            return response()->json([
                'updated' => true,
                'data' => $infoCus
            ]);
        }

        return response()->json([
            'created' => true,
            'data' => $infoCus
        ]);
    }

    /**
     * save note of customer
     * @param  QuickAddNoteRequest
     */
    public function quickAddNote(QuickAddNoteRequest $request)
    {
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $page = $user->pages()->where('fb_page_id', $request->fb_page_id)->first();
        $noteContent = $request->note_content;
        $cusId = $request->customer_id;
        $name = $request->name_cus;
        $fbId = $request->fb_id_cus;
        $noteId = $request->note_id;
        $customer = $user->customersUser()->find($cusId);
        if ($customer) {
            $note = $customer->notes()->find($noteId);
            if (! $note) {
                $newNoteId = $this->createNoteCustomer($cusId, $noteContent);
                $this->updateForConver($user, $customer, ['has_note' => true]);
                $justNote = CustomerNote::select('updated_at', 'note_content', 'id')->find($newNoteId);
                return response()->json([
                    'saved' => true,
                    'note' => $justNote,
                    'customer' => $customer
                ]);
            } else {
                $noteId = $this->updateNoteCustomer($note->id, $noteContent);
                $this->updateForConver($user, $customer, ['has_note' => true]);
                $justNote = CustomerNote::select('updated_at', 'note_content', 'id')->find($note->id);
                return response()->json([
                    'updated' => true,
                    'note' => $justNote,
                    'customer' => $customer
                ]);
            }
        } else {
            if ($page) {
                $newCus = Customer::updateOrCreate(
                    [
                        'user_id' => $adminId,
                        'fb_page_id' => $page->fb_page_id,
                        'fb_id_cus' => $fbId
                    ],
                    [
                        'staff_id' => $user->id,
                        'name_cus' => $name,
                    ]
                );
                $newNoteId = $this->createNoteCustomer($newCus->id, $noteContent);
                $justNote = CustomerNote::select('updated_at', 'note_content', 'id')->find($newNoteId);
                return response()->json([
                    'saved' => true,
                    'note' => $justNote,
                    'customer' => $newCus
                ]);
            }
        }
        return response()->json([
            'message' => 'Không tìm thấy dối tượng'
        ], 302);
    }

    public function destroyNote($customerId, $noteId)
    {
        $user = User::find(Auth::id());
        $customer = $user->customersUser()->find($customerId);
        if ($customer) {
            $note = $customer->notes()->find($noteId);
            if ($note) {
                $note->delete();
                return response()->json([
                    'success' => 1,
                    'message' => 'Xóa thành công'
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'message' => 'Không tồn tại'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Không tồn tại',
                'success' => 0
            ]);
        }
    }

    public function detailCustomer($id)
    {
        $user = Auth::user();
        $adminId = $user->adminId();
        $customer = $user->customersUser()
        ->with(['notes' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'groups', 'orders'])
        ->find($id);
        if ($customer) {
            $customer->count_conversations =(new ConverRespository($adminId))
                                                ->getCount([
                                                    ['user_id', $adminId],
                                                    ['from_id', $customer->fb_id_cus],
                                                ]);
        }
        return response()->json([
            'customer' => $customer
        ]);
    }

    public function updateDetailCustomer($id, CreateCustomerRequest $request)
    {
        $notes = $request->notes;
        $groups = $request->groups;
        $notesDel = $request->notes_del;
        $user = Auth::user();
        $customer = $user->customersUser()->find($id);
        if ($customer) {
            $customer->update([
                'name_cus' => $request->name_cus,
                'phone_cus' => $request->phone_cus,
                'email_cus' => $request->email_cus,
                'address_cus' => $request->address_cus,
            ]);

            foreach ($notes as $note) {
                if (isset($note['id']) && $note['id']) {
                    $this->updateNoteCustomer($note['id'], $note['note_content']);
                } else {
                    $this->createNoteCustomer($id, $note['note_content']);
                }
            }

            //delete note of customer
            $arr = $customer->notes()->whereIn('id', $notesDel)->get();
            foreach ($arr as $entity) {
                $entity->delete();
            }

            $customer->groups()->detach();
            $arr = [];
            foreach ($groups as $group) {
                $gr = GroupCustomer::find($group['id']);
                if ($gr) {
                    array_push($arr, $gr->id);
                }
            }
            $customer->groups()->attach($arr);

            return response()->json([
                'updated' => true
            ]);
        }
        return response()->json([
            'message' => 'Không tìm thấy đối tượng'
        ], 302);
    }

    public function exportFileCustomer($type)
    {
        $customers = Auth::user()
                            ->customersUser()
                            ->select('name_cus', 'fb_id_cus', 'phone_cus', 'email_cus', 'address_cus', 'banned', 'updated_at')
                            ->withCount('orders')
                            ->get()
                            ->toArray();
        $customers = json_decode(json_encode($customers), true);

        $header =  ['Tên khách hàng', 'ID Facebook', 'Số điện thoại', 'Email', 'Địa chỉ', 'Khóa', 'Lần hoạt động cuối', 'Số đơn hàng'];
        array_unshift($customers, $header);

        return \Excel::create('customers_list', function($excel) use ($customers) {

            $excel->sheet('customers', function($sheet) use ($customers)

            {

                $sheet->fromArray($customers, null, 'A1', false, false);

            });

        })->download($type);
    }

    public function destroyCustomers(Request $request)
    {
        $ids = $request->ids;
        $user = Auth::user();
        $arrNeed = [];
        if (is_array($ids)) {
            $customers = $user->customersUser()->whereIn('id', $ids)->get();
            foreach ($customers as $cus) {
                $notes = $cus->notes()->get();
                foreach ($notes as $note) {
                    $note->delete();
                }
                array_push($arrNeed, $cus->id);
                $cus->delete();
            }
            return response()->json([
                'success' => true
            ]);
        } else {
            $customer = $user->customersUser()->find($ids);
            if ($customer) {
                $notes = $customer->notes()->get();
                foreach ($notes as $note) {
                    $note->delete();
                }
                $customer->delete();
                return response()->json([
                    'success' => true
                ]);
            }
            return response()->json([
                'message' => 'Không tìm thấy đối tượng'
            ], 302);
        }
    }

    public function pinGroupForCus($id = null, Request $request)
    {
        $request->validate([
            'groupId' => 'required',
            'fb_page_id' => 'required',
            'name' => 'required',
            'fbId' => 'required'
        ]);
        $user = User::find(Auth::id());
        $groupId = $request->groupId;
        $fbPageId = $request->fb_page_id;
        $name = $request->name;
        $fbId = $request->fbId;
        $group = $user->groupsCustomer()->find($groupId);
        $customer = $user->customersUser()->find($id);
        if ($customer) {
            $hasGroup = $customer->groups()->find(optional($group)->id);
            if (!$hasGroup) {
                $customer->groups()->attach($groupId);
                $infoCus = Customer::with('groups')->find($customer->id);
                return response()->json([
                    'success' => true,
                    'customer' => $infoCus
                ]);
            } else {
                return response()->json([
                    'message' => 'Nhóm khách hàng đã tồn tại'
                ], 500);
            }
        } else {
            $page = $user->pages()->where('fb_page_id', $fbPageId)->first();
            if ($page && $group) {
                $newCus = Customer::updateOrCreate(
                    [
                        'user_id' => $user->adminId(),
                        'fb_page_id' => $page->fb_page_id,
                        'fb_id_cus' => $fbId
                    ],
                    [
                        'staff_id' => $user->id,
                        'name_cus' => $name
                    ]
                );
                $newCus->groups()->attach($group->id);
                $infoCus = Customer::with('groups')->find($newCus->id);
                return response()->json([
                    'success' => true,
                    'customer' => $infoCus
                ]);
            }
            return response()->json([
                'message' => 'Không tìm thấy đối tượng'
            ], 500);
        }
    }

    public function outGroupForCus($id, $groupId)
    {
        $user = Auth::user();
        $customer = $user->customersUser()->find($id);
        $group = GroupCustomer::find($groupId);
        if ($customer && $group) {
            $customer->groups()->detach($group->id);
            $infoCus = Customer::with('groups')->find($customer->id);
            return response()->json([
                'success' => true,
                'customer' => $infoCus
            ]);
        }
        return response()->json([
            'message' => 'Không tìm thấy đối tượng'
        ], 302);
    }

    public function updateForConver(User $user, Customer $customer, array $extra = [])
    {
        $adminId = $user->adminId();
        $update = [];
        if (isset($extra['has_note'])) {
            $update['has_note'] = $extra['has_note'];
        }
        if (isset($extra['has_order'])) {
            $update['has_order'] = $extra['has_order'];
        }
        $condict = [
            ['user_id', $adminId],
            ['from_id', $customer->fb_id_cus],
        ];
        (new ConverRespository($adminId))->updateCondict($condict, $update);
    }
}
