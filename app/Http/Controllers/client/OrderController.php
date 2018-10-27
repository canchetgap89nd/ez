<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\QuickCreateOrder;
use App\Http\Requests\QuickCreateConfirmOrder;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\SaveOrderRequest;
use App\models\Order;
use App\models\Customer;
use App\models\ProductOrder;
use App\models\VietNamProvince;
use App\models\VietNamDistrict;
use App\models\VietNamWard;
use App\models\Payment;
use App\models\Transport;
use App\models\Product;
use App\models\Campaign;
use App\User;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\client\ProductController;
use App\Http\Controllers\client\CustomerController;
use App\Jobs\SaleStatistical;
use App\Jobs\RollBackSaleData;
use App\models\Mongo\Conversation;

class OrderController extends Controller
{
    public function quickCreate(QuickCreateOrder $request)
    {
    	$infoOrder = $request->infoOrder;
    	$products = $request->products;
    	$infoCus = $request->infoCus;
        $fbPageId = $request->fbPageId;
    	$user = User::find(Auth::id());
        $page = $user->pages()->where('fb_page_id', $fbPageId)->first();
        if ($page) {
            $page_id = $page->id;
        	$customer = isset($infoCus['id']) ? $this->checkCustomer($infoCus['id']) : null;
        	if ($customer) {
        		$infoOrder['cusId'] = $customer->id;
                (new CustomerController)->updateForConver($user, $customer, ['has_order' => true]);
        	} else {
                $newCus = Customer::create([
                    'user_id' => $user->adminId(),
                    'fb_page_id' => $page->fb_page_id,
                    'staff_id' => $user->id,
                    'fb_id_cus' => $infoCus['fb_id_cus'],
                    'name_cus' => $infoCus['name_cus'],
                    'phone_cus' => isset($infoCus['phone_cus']) ? $infoCus['phone_cus'] : null,
                    'email_cus' => isset($infoCus['email_cus']) ? $infoCus['email_cus'] : null,
                    'address_cus' => isset($infoCus['address_cus']) ? $infoCus['address_cus'] : null,
                    'banned' => false
                ]);
                $newCus->pages()->attach([
                    $page_id => [
                        'fb_page_id' => $page->fb_page_id
                    ]
                ]);
                $infoOrder['cusId'] = $newCus->id;
        	}

            $newOrder = Order::create([
                'order_code' => $this->generalCode(),
                'user_id' => $user->adminId(),
                'staff_id' => $user->id,
                'fb_page_id' => $page->fb_page_id,
                'page_id' => $page_id,
                'cus_id' => $infoOrder['cusId'],
                'name_receive' => $infoOrder['nameReceive'],
                'phone_receive' => isset($infoOrder['phoneReceive']) ? $infoOrder['phoneReceive'] : null,
                'province_id' => isset($infoOrder['provinceId']) ? $infoOrder['provinceId'] : null,
                'district_id' => isset($infoOrder['districtId']) ? $infoOrder['districtId'] : null,
                'ward_id' => isset($infoOrder['wardId']) ? $infoOrder['wardId'] : null,
                'ad_receive' => isset($infoOrder['addressReceive']) ? $infoOrder['addressReceive'] : null,
                'discount' => isset($infoOrder['discount']) ? $infoOrder['discount'] : null,
                'ship_fee' => isset($infoOrder['shipFee']) ? $infoOrder['shipFee'] : 0,
                'total_value' => isset($infoOrder['totalValue']) ? $infoOrder['totalValue'] : 0,
                'value_has_sale' => isset($infoOrder['valueHasSale']) ? $infoOrder['valueHasSale'] : 0,
                'total_amount' => isset($infoOrder['totalAmount']) ? $infoOrder['totalAmount'] : 0,
                'note_order' => isset($infoOrder['note']) ? $infoOrder['note'] : null,
                'status_order' => 'NEW',
                'time_confirmed' => date('Y-m-d H:i:s')
            ]);

            $arr = [];
            foreach ($products as $product) {
                $prod = $user->productUsers()->find($product['id']);
                if ($prod) {
                    $campId = $product['hasSale'] ? $product['campId'] : null;
                    $arr[$product['id']] =  [
                                                'prod_name' => $prod->prod_name,
                                                'prod_code' => $prod->prod_code,
                                                'properties' => $prod->properties,
                                                'quantity' => $product['quantity'],
                                                'price' => $product['price'],
                                                'price_sale' => $product['priceSale'],
                                                'camp_id' => $campId
                                            ];
                }
            }

            $newOrder->products()->attach($arr);
        	return response()->json([
        		'id' => $newOrder->id,
                'customer_id' => $infoOrder['cusId']
        	]);
        }
        return response()->json([
            'message' => 'Không tồn tại Fanpage'
        ], 500);
    }

    public function quickCreateConfirm(QuickCreateConfirmOrder $request)
    {
        $infoOrder = $request->infoOrder;
        $products = $request->products;
        $infoCus = $request->infoCus;
        $fbPageId = $request->fbPageId;
        $user = Auth::user();
        $page = $user->pages()->where('fb_page_id', $fbPageId)->first();
        if ($page) {
            $page_id = $page->id;
            foreach ($products as $k => $product) {
                $products[$k]['prod_id'] = $product['id'];
            }
            $check = $this->hasInStorage($products);
            if ($check['has']) {
                $customer = isset($infoCus['id']) ? $this->checkCustomer($infoCus['id']) : null;
                if ($customer) {
                    $infoOrder['cusId'] = $customer->id;
                    // update for conversation
                    (new CustomerController)->updateForConver($user, $customer, ['has_order' => true]);
                } else {
                    $newCus = Customer::create([
                        'staff_id' => $user->id,
                        'fb_page_id' => $page->fb_page_id,
                        'user_id' => $user->adminId(),
                        'fb_id_cus' => $infoCus['fb_id_cus'],
                        'name_cus' => $infoCus['name_cus'],
                        'phone_cus' => isset($infoCus['phone_cus']) ? $infoCus['phone_cus'] : null,
                        'email_cus' => isset($infoCus['email_cus']) ? $infoCus['email_cus'] : null,
                        'address_cus' => isset($infoCus['address_cus']) ? $infoCus['address_cus'] : null,
                        'banned' => false
                    ]);
                    $newCus->pages()->attach([
                        $page_id => [
                            'fb_page_id' => $page->fb_page_id
                        ]
                    ]);
                    $infoOrder['cusId'] = $newCus->id;
                }

                $newOrder = Order::create([
                    'order_code' => $this->generalCode(),
                    'user_id' => $user->adminId(),
                    'staff_id' => $user->id,
                    'fb_page_id' => $page->fb_page_id,
                    'cus_id' => $infoOrder['cusId'],
                    'page_id' => $page_id,
                    'name_receive' => $infoOrder['nameReceive'],
                    'phone_receive' => $infoOrder['phoneReceive'],
                    'province_id' => $infoOrder['provinceId'],
                    'district_id' => $infoOrder['districtId'],
                    'ward_id' => $infoOrder['wardId'],
                    'ad_receive' => $infoOrder['addressReceive'],
                    'discount' => $infoOrder['discount'],
                    'ship_fee' => $infoOrder['shipFee'],
                    'total_value' => $infoOrder['totalValue'],
                    'value_has_sale' => $infoOrder['valueHasSale'],
                    'total_amount' => $infoOrder['totalAmount'],
                    'note_order' => $infoOrder['note'],
                    'status_order' => 'CONFIRM',
                    'time_confirmed' => date('Y-m-d H:i:s')
                ]);

                $newOrder->transports()->create([
                    'amount' => $infoOrder['totalAmount'],
                    'user_id' => $user->adminId(),
                    'staff_id' => $user->id
                ]);
                
                $arr = [];
                foreach ($products as $product) {
                    $prod = $user->productUsers()->find($product['id']);
                    if ($prod) {
                        $campId = $product['hasSale'] ? $product['campId'] : null;
                        $arr[$product['id']] =  [
                                                    'prod_name' => $prod->prod_name,
                                                    'prod_code' => $prod->prod_code,
                                                    'properties' => $prod->properties,
                                                    'quantity' => $product['quantity'],
                                                    'price' => $product['price'],
                                                    'price_sale' => $product['priceSale'],
                                                    'camp_id' => $campId
                                                ];
                    }
                }

                $newOrder->products()->attach($arr);

                SaleStatistical::dispatch($user, $newOrder)->onQueue('statistic');

                $this->exportProductsOfOrder($newOrder->id);

                return response()->json([
                    'id' => $newOrder->id,
                    'customer_id' => $infoOrder['cusId']
                ]);
            } else {
                return response()->json([
                    'message' => $check['message']
                ], 500);
            }
        }
        return response()->json([
            'message' => 'Không tồn tại Fanpage'
        ], 500);
    }

    /**
     * create infomation of order
     * @param  array  $data [userId, cusId, nameReceive, phoneReceive, provinceId, districtId, wardId, addressReceive, discount, shipFee, otherFee, totalValue, note, deadline]
     * @return [int]       id new order
     */
    public function createCore(User $user, array $data)
    {
    	$newOrder = new Order;
    	$newOrder->order_code = $this->generalCode();
    	$newOrder->user_id = $user->adminId();
        $newOrder->staff_id = $user->id;
        $newOrder->fb_page_id = $data['fbPageId'];
    	$newOrder->cus_id = $data['cusId'];
        $newOrder->page_id = isset($data['pageId']) ? $data['pageId'] : null;
    	$newOrder->name_receive = $data['nameReceive'];
    	$newOrder->phone_receive = isset($data['phoneReceive']) ? $data['phoneReceive'] : null;
    	$newOrder->email_receive = isset($data['emailReceive']) ? $data['emailReceive'] : null;
    	$newOrder->province_id = isset($data['provinceId']) ? $data['provinceId'] : null;
    	$newOrder->district_id = isset($data['districtId']) ? $data['districtId'] : null;
    	$newOrder->ward_id = isset($data['wardId']) ? $data['wardId'] : null;
    	$newOrder->ad_receive = isset($data['addressReceive']) ? $data['addressReceive'] : null;
    	$newOrder->discount = $data['discount'];
    	$newOrder->ship_fee = $data['shipFee'];
    	$newOrder->other_fee = isset($data['otherFee']) ? $data['otherFee'] : 0;
    	$newOrder->total_value = $data['totalValue'];
    	$newOrder->value_has_sale = $data['valueHasSale'];
    	$newOrder->total_amount = $data['totalAmount'];
    	$newOrder->total_pay = isset($data['totalPay']) ? $data['totalPay'] : 0;
    	$newOrder->note_order = isset($data['note']) ? $data['note'] : null;
    	$newOrder->status_order = 'NEW';
    	$newOrder->deadline_order = isset($data['deadline']) ? $data['deadline'] : null;
    	$newOrder->save();
    	return $newOrder->id;
    }

    public function checkCustomer($id)
    {
    	$customer = Auth::user()->customersUser()->find($id);
    	if ($customer) {
    		return $customer;
    	}
    	return null;
    }

    /**
     * create infomation customer
     * @param  array  $data [userId, fbId, name, phone, email, address]
     * @return [int]       [id of new customer]
     */
    public function createCustomer(User $user, array $data)
    {
    	$newCus = new Customer;
    	$newCus->user_id = $user->adminId();
        $newCus->staff_id = $user->id;
    	$newCus->fb_id_cus = isset($data['fbId']) ? $data['fbId'] : null;
    	$newCus->name_cus = $data['name'];
    	$newCus->phone_cus = isset($data['phone']) ? $data['phone'] : null;
    	$newCus->email_cus = isset($data['email']) ? $data['email'] : null;
    	$newCus->address_cus = isset($data['address']) ? $data['address'] : null;
    	$newCus->save();

        if (isset($data['pageId'])) {
            $page = Auth::user()->pages()->find($data['pageId']);
            if ($page) {
                $newCus->pages()->attach([
                    $page->id => [
                        'fb_page_id' => $page->fb_page_id
                    ]
                ]);
            }
        }

    	return $newCus->id;
    }

    /**
     * create product has in order
     * @param  [array] $data [prodId, orderId, quantity, price, priceSale, campId, hasSale]
     * @return [int]       [id new ProductOrder]
     */
    public function createProductsOrder(array $data)
    {
    	$newProdOrder = new ProductOrder;
    	$newProdOrder->prod_id = $data['prodId'];
    	$newProdOrder->order_id = $data['orderId'];
    	$newProdOrder->quantity = $data['quantity'];
    	$newProdOrder->price = $data['price'];
    	$newProdOrder->price_sale = $data['priceSale'];
    	$newProdOrder->camp_id = $data['hasSale'] ? $data['campId'] : null;
    	$newProdOrder->save();
    	return $newProdOrder->id;
    }

    public function generalCode()
    {
        $user = Auth::user();
        $count = $user->ordersUser()->count();
        $count = $count + 1;
        $num = $code = str_pad($count, 3, '0', STR_PAD_LEFT);
        $check = $user->ordersUser()->where('order_code', $num)->count();
        $index = 1;
        while ($check) {
            $code = $num . strtoupper(str_random($index));
            $check = $user->ordersUser()->where('order_code', $code)->count();
            $index++;
        }
        return $code;
    }

    public function getListOrder(Request $request)
    {
        $user = User::find(Auth::id());
        $code = $request->code;
        $typeDate = $request->checkDate == 'CREATE' ? 'created_at' : 'updated_at';
        $statusOrder = $request->statusOrder;
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $staff = $request->staff;
        $arrWhere = [
            ['orders.order_code', 'like', "%$code%"],
            ['orders.status_order', 'like', "%$statusOrder%"],
        ];

        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['orders.'.$typeDate, '>=', $timeFrom]);
        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['orders.'.$typeDate, '<=', $timeTo]);
        }
        if ($staff) {
            array_push($arrWhere, ['orders.user_id', $staff]);
        }

        if ($user->isAdmin() || $user->isManager()) {
        	return DB::table('orders')
                        ->leftJoin('users', 'orders.staff_id', '=', 'users.id')
                        ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                        ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                        ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                        ->select('users.name', 'orders.*', 'viet_nam_wards.name_ward', 'viet_nam_districts.name_district', 'viet_nam_provinces.name as name_province')
                        ->where('orders.user_id', $user->adminId())
                        ->where($arrWhere)
                        ->orderBy($typeDate, 'desc')
                        ->paginate(10)
                        ->toJson();
        }
        return DB::table('orders')
                        ->leftJoin('users', 'orders.staff_id', '=', 'users.id')
                        ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                        ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                        ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                        ->select('users.name', 'orders.*', 'viet_nam_wards.name_ward', 'viet_nam_districts.name_district', 'viet_nam_provinces.name as name_province')
                        ->where('orders.user_id', $user->adminId())
                        ->where('orders.staff_id', $user->id)
                        ->where($arrWhere)
                        ->orderBy($typeDate, 'desc')
                        ->paginate(10)
                        ->toJson();
    }

    public function getCreateOrder()
    {
    	$provinces = VietNamProvince::all();
    	return response()->json([
    		'provinces' => $provinces
    	]);
    }

    public function createOrder(CreateOrderRequest $request)
    {
    	$infoCus = $request->infoCus;
    	$infoOrder = $request->infoOrder;
    	$products = $request->products;
    	$payments = $request->payments;
    	$transport = $request->transport;
        $user = User::find(Auth::id());
        if (isset($infoCus['id'])) {
            $cus = $user->customersUser()->find($infoCus['id']);
            if ($cus) {
                $infoOrder['cusId'] = $cus->id;
                $infoOrder['fbPageId'] = $cus->fb_page_id;
            } else {
                return response()->json([
                    'message' => 'Không tồn tại khách hàng'
                ], 302);
            }
        } else {
            $infoOrder['cusId'] = $this->createCustomer($user, $infoCus);
            $newCus = $user->customersUser()->find($infoOrder['cusId']);
            $infoOrder['fbPageId'] = $newCus->fb_page_id;
        }

    	$orderId = $this->createCore($user, $infoOrder);

    	$this->createTransportOrder($user, $orderId, $transport['amount'], $transport['note']);

        $arr = [];
        foreach ($products as $product) {
            $prod = $user->productUsers()->find($product['id']);
            if ($prod) {
                $campId = $product['hasSale'] ? $product['campId'] : null;
                $arr[$product['id']] =  [
                                            'prod_name' => $prod->prod_name,
                                            'prod_code' => $prod->prod_code,
                                            'properties' => $prod->properties,
                                            'quantity' => $product['quantity'],
                                            'price' => $product['price'],
                                            'price_sale' => $product['priceSale'],
                                            'camp_id' => $campId
                                        ];
            }
        }

        Order::find($orderId)->products()->attach($arr);

        foreach ($payments as $pay) {
            $pay['orderId'] = $orderId;
            $pay['customer_id'] = $infoOrder['cusId'];
            $this->createPayOrder($user, $pay);
    	}

    	return response()->json([
    		'id' => $orderId
    	]);
    }

    public function createTransportOrder(User $user, int $orderId, $amount, $note)
    {
        $newTran = Transport::create([
            'note' => $note,
            'amount' => $amount,
            'order_id' => $orderId,
            'staff_id' => $user->id,
            'user_id' => $user->adminId()
        ]);
    	return $newTran->id;
    }

    /**
     * create payment of order
     * @param  array  $data [orderId, amount, refund]
     * @return [type]       [description]
     */
    public function createPayOrder(User $user, array $data)
    {
        $newPay = Payment::create([
            'user_id' => $user->adminId(),
            'staff_id' => $user->id,
            'order_id' => $data['orderId'],
            'amount_pay' => $data['amount'],
            'refund' => $data['refund'],
            'customer_id' => $data['customer_id']
        ]);
    	return $newPay->id;
    }

    public function getProductsOrder(int $orderId)
    {
        $prods = ProductOrder::where('order_id', $orderId)->get();
        foreach ($prods as $key => $prod) {
            $product = Product::find($prod->prod_id);
            if ($product) {
                $prods[$key]->prod_thumb = $product->prod_thumb;
            } else {
                $prods[$key]->prod_thumb = '';
            }
        }
        return $prods;
    }

    public function getEditOrder($id)
    {
        $user = User::find(Auth::id());
    	$order = $user->ordersUser()
    					->find($id);
    	if ($order) {
    		$products = $this->getProductsOrder($id);
    		$payments = Payment::where('order_id', $id)->get();
    		$infoCus = Customer::find($order->cus_id);
    		$creater = User::select('name', 'id')->find($order->user_id);
    		$transport = Transport::where('order_id', $id)->first();
    		return response()->json([
    			'infoOrder' => $order,
    			'infoCus' => $infoCus,
    			'payments' => $payments,
    			'products' => $products,
    			'creater' => $creater,
    			'transport' => $transport
    		]);
    	}
    	return response()->json([
    		'message' => 'Không tìm thấy đơn hàng'
    	], 500);
    }

    public function updateOrder(UpdateOrderRequest $request)
    {
    	$infoOrder = $request->infoOrder;
    	$done = false;
    	$message = null;
        $user = User::find(Auth::id());
        $order = $user->ordersUser()
                        ->find($infoOrder['id']);
    	if ($order) {
            $products = ProductOrder::where('order_id', $order->id)->get();
	    	switch ($request->status) {
	    		case 'CONFIRM':
	    			$confirm = $this->confirmOrder($request);
	    			if ($confirm['confirmed']) {
	    				$done = true;
                        $order = Order::find($order->id);
                        SaleStatistical::dispatch($user, $order)->onQueue('statistic');
	    			} else {
	    				$message = $confirm['message'];
	    			}
	    			break;
	    		case 'SENDING':
	    			if ($order->time_confirmed !== null) {
	    				$confirm = $this->confirmAgain($request);
	    				if ($confirm['confirmed']) {
	    					$this->sendingOrder($order->id);
	    					$done = true;
                            $order = Order::find($order->id);
                            RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
                            SaleStatistical::dispatch($user, $order)->onQueue('statistic');
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			} else {
		    			$confirm = $this->confirmOrder($request);
	    				if ($confirm['confirmed']) {
	    					$this->sendingOrder($order->id);
	    					$done = true;
                            $order = Order::find($order->id);
                            SaleStatistical::dispatch($user, $order)->onQueue('statistic');
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			}
	    			break;
	    		case 'SENT':
	    			if ($order->time_confirmed !== null) {
	    				$confirm = $this->confirmAgain($request);
	    				if ($confirm['confirmed']) {
	    					$this->sentOrder($order->id);
	    					$done = true;
                            $order = Order::find($order->id);
                            RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
                            SaleStatistical::dispatch($user, $order)->onQueue('statistic');
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			} else {
	    				$confirm = $this->confirmOrder($request);
	    				if ($confirm['confirmed']) {
	    					$this->sentOrder($order->id);
	    					$done = true;
                            $order = Order::find($order->id);
                            SaleStatistical::dispatch($user, $order)->onQueue('statistic');
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			}
                    $this->updateCampaignOfOrder($products);
	    			break;
	    		case 'REFUNDING':
	    			if ($order->time_confirmed !== null) {
	    				$confirm = $this->confirmAgain($request);
	    				if ($confirm['confirmed']) {
	    					$this->refundingOrder($order->id);
	    					$done = true;
                            $order = Order::find($order->id);
                            RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			} else {
	    				$confirm = $this->confirmOrder($request);
	    				if ($confirm['confirmed']) {
	    					$this->refundingOrder($order->id);
	    					$done = true;
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			}
                    $this->updateCampaignOfOrder($products, true);
	    			break;
	    		case 'REFUNDED':
	    			if ($order->time_confirmed !== null) {
	    				$confirm = $this->confirmAgain($request);
	    				if ($confirm['confirmed']) {
	    					$this->refundedOrder($order->id);
	    					$done = true;
                            $order = Order::find($order->id);
                            RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			} else {
	    				$confirm = $this->confirmOrder($request);
	    				if ($confirm['confirmed']) {
	    					$this->refundedOrder($order->id);
	    					$done = true;
	    				} else {
	    					$message = $confirm['message'];
	    				}
	    			}

                    if ($order->time_refunding === null) {
                        $this->updateCampaignOfOrder($products, true);
                    }
	    			break;
	    		default:
	    			break;
	    	}
    	} else {
    		$message = 'Không tìm thấy đơn hàng';
    	}

    	if ($done) {
    		return response()->json([
    			'updated' => true
    		]);
    	}
    	return response()->json([
    		'message' => $message
    	], 302);
    }

    /**
     * confirm order
     * @param  [object] $data [infoOrder, infoCus, productsDel, payments, transport]
     * @return [type]
     */
    public function confirmOrder($data)
    {
    	$products = $data->products;
    	$hasStorage = $this->hasInStorage($products);
        $user = User::find(Auth::id());
        $order = $user->ordersUser()->find($data->infoOrder['id']);
    	if ($hasStorage['has'] && $order) {
	    	$infoOrder = $data->infoOrder;
	    	$infoOrder['status_order'] = "CONFIRM";
	    	$infoCus = $data->infoCus;
	    	$productsDel = $data->productsDel;
	    	$payments = $data->payments;
	    	$transport = $data->transport;
	    	if (isset($transport['id']) && $transport['id']) {
	    		$this->updateTransportOrder($transport);
	    	} else {
	    		$this->createTransportOrder($user, $infoOrder['id'], $transport['amount'], $transport['note']);
	    	}
	    	$this->updateCore($infoOrder);
	    	$this->updateInfoCus($infoCus);
	    	$this->updateProductsOrder($products);
	    	$this->removeProductsOfOrder($productsDel);
	    	$this->updatePaymentsOrder($user, $order, $payments);
	    	$this->exportProductsOfOrder($infoOrder['id']);
	    	return [
	    		'confirmed' => true
	    	];
    	}
		return [
    		'confirmed' => false,
    		'message' => $hasStorage['message']
    	];
    }

    public function confirmAgain($data)
    {
    	$products = $data->products;
    	$infoOrder = $data->infoOrder;
        $infoOrder = $data->infoOrder;
    	$infoCus = $data->infoCus;
    	$productsDel = $data->productsDel;
    	$payments = $data->payments;
    	$transport = $data->transport;
        $user = User::find(Auth::id());
        $order = $user->ordersUser()->find($infoOrder['id']);
        if ($infoOrder['status_order'] !== null && $infoOrder['status_order'] !== 'NEW') {
            $exportMore = [];
    		$refundProd = [];
        	foreach ($products as $key => $product) {
        		$cur = ProductOrder::where('prod_id', $product['prod_id'])
        									->where('order_id', $infoOrder['id'])
        									->first();
        		$prodInfo = Product::find($product['prod_id']);
        		if ($cur && $prodInfo) {
        			$diff = $product['quantity'] - $cur->quantity;
        			$storage = $prodInfo->prod_quantity - $prodInfo->count_sold;
        			if ($diff > 0) {
    	    			if ($diff > $storage) {
    	    				return [
    	    					'confirmed' => false,
    	    					'message' => 'Sản phẩm ' . $prodInfo->prod_name . ' chỉ còn lại ' . $storage . ' sản phẩm trong kho'
    	    				];
    	    			} else {
    	    				$exportMore[] = [
    	    					'id' => $product['prod_id'],
    	    					'quantity' => $diff,
    	    					'price' => $product['price_sale']
    	    				];
    	    			}
        			} else {
        				$refundProd[] = [
        					'id' => $product['prod_id'],
        					'quantity' => abs($diff),
        					'price' => $product['price_sale']
        				];
        			}
        		} else
        		if (!$cur && $prodInfo) {
        			$storage = $prodInfo->prod_quantity - $prodInfo->count_sold;
        			if ($product['quantity'] > $storage) {
        				return [
        					'confirmed' => false,
        					'message' => 'Sản phẩm ' . $prodInfo->prod_name . ' chỉ còn lại ' . $storage . ' sản phẩm trong kho'
        				];
        			} else if ($product['quantity'] < $storage) {
        				$exportMore[] = [
        					'id' => $product['prod_id'],
        					'quantity' => $product['quantity'],
        					'price' => $product['price_sale']
        				];
        			}
        		}
        	}
        	$this->makeExportProducts($exportMore);
            //merge with product has delete
            foreach ($productsDel as $item) {
                $cur = ProductOrder::where('prod_id', $item['prod_id'])
                                            ->where('order_id', $infoOrder['id'])
                                            ->first();
                if ($cur && ($cur->quantity == $item['quantity'])) {
                    $check = true;
                    foreach ($refundProd as $el) {
                        if ($el['id'] == $item['prod_id']) {
                            $check = false;
                        }
                    }
                    if ($check) {
                        $tg = [];
                        $tg['id'] = $item['prod_id'];
                        $tg['price'] = $item['price'];
                        $tg['quantity'] = $item['quantity'];
                        $refundProd[] = $tg;
                    }
                }
            }
            $this->makeImportProducts($refundProd);
        }

    	if (isset($transport['order_id'])) {
    		$this->updateTransportOrder($transport);
    	} else {
    		$this->createTransportOrder($user, $infoOrder['id'], $transport['amount'], $transport['note']);
    	}
    	$this->updateCore($infoOrder);
    	$this->updateInfoCus($infoCus);
    	$this->updateProductsOrder($products);
    	$this->removeProductsOfOrder($productsDel);
    	$this->updatePaymentsOrder($user, $order, $payments);
		return [
    		'confirmed' => true
    	];
    }

    /**
     * check storage of products
     * @param  array   $products [list product of order]
     * @return boolean           [description]
     */
    public function hasInStorage(array $products)
    {
    	foreach ($products as $product) {
    		$prodInfo = Product::find($product['prod_id']);
    		if ($prodInfo) {
    			$storage = $prodInfo->prod_quantity - $prodInfo->count_sold;
    			if ($product['quantity'] > $storage) {
    				return [
    					'has' => false,
    					'message' => 'Sản phẩm ' . $prodInfo->prod_name . ' chỉ còn lại ' . $storage . ' sản phẩm trong kho'
    				];
    			}
    		} else {
                return [
                    'has' => false,
                    'message' => 'Sản phẩm không tồn tại'
                ];
            }
    	}
    	return ['has' => true];
    }

    public function updateCore(array $data)
   	{
   		$order = Order::find($data['id']);
   		if ($order) {
	    	$order->name_receive = isset($data['name_receive']) ? $data['name_receive'] : $order->name_receive;
	    	$order->phone_receive = isset($data['phone_receive']) ? $data['phone_receive'] : $order->phone_receive;
	    	$order->email_receive = isset($data['email_receive']) ? $data['email_receive'] : $order->email_receive;
	    	$order->province_id = isset($data['province_id']) ? $data['province_id'] : $order->province_id;
	    	$order->district_id = isset($data['district_id']) ? $data['district_id'] : $order->district_id;
	    	$order->ward_id = isset($data['ward_id']) ? $data['ward_id'] : $order->ward_id;
	    	$order->ad_receive = isset($data['ad_receive']) ? $data['ad_receive'] : $order->ad_receive;
	    	$order->discount = isset($data['discount']) ? $data['discount'] : $order->discount;
	    	$order->ship_fee = isset($data['ship_fee']) ? $data['ship_fee'] : $order->ship_fee;
	    	$order->other_fee = isset($data['other_fee']) ? $data['other_fee'] : $order->other_fee;
	    	$order->total_value = isset($data['total_value']) ? $data['total_value'] : $order->total_value;
	    	$order->value_has_sale = isset($data['value_has_sale']) ? $data['value_has_sale'] : $order->value_has_sale;
	    	$order->total_amount = isset($data['total_amount']) ? $data['total_amount'] : $order->total_amount;
	    	$order->total_pay = isset($data['total_pay']) ? $data['total_pay'] : $order->total_pay;
	    	$order->note_order = isset($data['note_order']) ? $data['note_order'] : $order->note_order;
	    	$order->deadline_order = isset($data['deadline_order']) ? $data['deadline_order'] : $order->deadline_order;
	    	if (isset($data['status_order'])) {
		    	switch ($data['status_order']) {
		    		case 'CONFIRM':
		    			$order->time_confirmed = date('Y-m-d H:i:s');
		    			$order->status_order = 'CONFIRM';
		    			break;
		    		case 'SENDING':
		    			$order->time_sending = date('Y-m-d H:i:s');
		    			$order->status_order = 'SENDING';
		    			break;
		    		case 'SENT':
		    			$order->time_sent = date('Y-m-d H:i:s');
		    			$order->status_order = 'SENT';
		    			break;
		    		case 'REFUNDING':
		    			$order->time_refunding = date('Y-m-d H:i:s');
		    			$order->status_order = 'REFUNDING';
		    			break;
		    		case 'REFUNDED':
		    			$order->time_refunded = date('Y-m-d H:i:s');
		    			$order->status_order = 'REFUNDED';
		    			break;
		    		case 'CANCELED':
		    			$order->time_canceled = date('Y-m-d H:i:s');
		    			$order->status_order = 'CANCELED';
		    			break;
		    		default:
		    			break;
		    	}
	    	}

	    	$order->save();
	    	return $order->id;
   		}
   		return null;
   	}

   	public function updateInfoCus(array $data)
   	{
   		$customer = Auth::user()->customersUser()->find($data['id']);
   		if ($customer) {
   			$customer->fb_id_cus = isset($data['fb_id_cus']) ? $data['fb_id_cus'] : $customer->fb_id_cus;
   			$customer->name_cus = isset($data['name_cus']) ? $data['name_cus'] : $customer->name_cus;
   			$customer->phone_cus = isset($data['phone_cus']) ? $data['phone_cus'] : $customer->phone_cus;
   			$customer->email_cus = isset($data['email_cus']) ? $data['email_cus'] : $customer->email_cus;
   			$customer->address_cus = isset($data['address_cus']) ? $data['address_cus'] : $customer->address_cus;
   			$customer->save();
   			return $customer->id;
   		}
   		return null;
   	}

   	public function updateProductsOrder(array $products)
   	{
   		$prodAc = [];
        $user = Auth::user();
   		foreach ($products as $product) {
   			$item = ProductOrder::where('order_id', $product['order_id'])
   								->where('prod_id', $product['prod_id'])
   								->first();
   			if ($item) {
   				$item->quantity = isset($product['quantity']) ? $product['quantity'] : $item->quantity;
   				$item->save();
   			} else {
                $infoProd = $user->productUsers()->find($product['prod_id']);
                if ($infoProd) {
       				$newProd = new ProductOrder;
                    $newProd->prod_name = $infoProd->prod_name;
                    $newProd->prod_code = $infoProd->prod_code;
                    $newProd->properties = $infoProd->properties;
       				$newProd->prod_id = $product['prod_id'];
       				$newProd->order_id = $product['order_id'];
       				$newProd->quantity = $product['quantity'];
       				$newProd->price = $product['price'];
       				$newProd->price_sale = $product['price_sale'];
       				$newProd->camp_id = $product['hasSale'] ? $product['camp_id'] : null;
       				$newProd->save();
                }
   			}
   		}
   	}

   	private function removeProductsOfOrder(array $products)
   	{
   		$prodAc = [];
   		foreach ($products as $product) {
   			$item = ProductOrder::where('order_id', $product['order_id'])
   								->where('prod_id', $product['prod_id'])
   								->first();
   			if ($item) {
   				$item->delete();
   			}
   		}
   	}

   	private function updatePaymentsOrder(User $user, Order $order, array $payments)
   	{
        foreach ($payments as $pay) {
            $payItem = $order->payments()->find($pay['id']);
            if ($payItem) {
                $payItem->amount_pay = $pay['amount_pay'];
                $payItem->save();
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->cus_id,
                    'amount_pay' => $pay['amount_pay'],
                    'refund' => $pay['refund'],
                    'user_id' => $user->adminId(),
                    'staff_id' => $user->id
                ]);
   			}
   		}
   	}

   	private function updateTransportOrder(array $data)
   	{
   		$transport = Transport::where('order_id', $data['order_id'])->find($data['id']);
   		if ($transport) {
   			$transport->note = $data['note'];
   			$transport->amount = $data['amount'];
   			$transport->save();
   			return $transport->id;
   		}
   		return null;
   	}

   	private function sendingOrder($id)
   	{
   		$order = Auth::user()->ordersUser()->find($id);
   		if ($order) {
   			$dataOrder['id'] = $order->id;
   			$dataOrder['status_order'] = "SENDING";
   			$this->updateCore($dataOrder);
   		}
   	}

   	private function exportProductsOfOrder($id)
   	{
   		$prodsOrder = ProductOrder::where('order_id', $id)->get();
        $user = User::find(Auth::id());
		$inventory_ex = 0;
   		$quantity_ex = 0;
   		$amount_ex = 0;
   		$productsReal = [];
		foreach ($prodsOrder as $prodItem) {
			$product = Product::find($prodItem->prod_id);
			if ($product) {
				$inventory = $product->prod_quantity - $product->count_sold - $prodItem->quantity;
				$inventory_ex += $inventory;
				$quantity_ex += $prodItem->quantity;
				$amount_ex += $prodItem->price_sale * $prodItem->quantity;

				$prodItem->inventory = $inventory;
				array_push($productsReal, $prodItem);
			}
		}
        if (count($productsReal) > 0) {
			$dataEx['user_id'] = $user->id;
			$dataEx['quantity_ex'] = $quantity_ex;
			$dataEx['inventory_ex'] = $inventory_ex;
			$dataEx['amount_ex'] = $amount_ex;
			$dataProdExp['export_id'] = (new ProductController)->createExport($user, $dataEx);
			foreach ($productsReal as $product) {
				$dataProdExp['prod_id'] = $product->prod_id;
	   			$dataProdExp['quantity_ex'] = $product->quantity;
	   			$dataProdExp['price_ex'] = $product->price_sale;
	   			$dataProdExp['inventory_ex'] = $product->inventory;
	   			(new ProductController)->createProductExport($dataProdExp);
			}
        }
   	}

   	/**
   	 * export more product quanity of order in of sent order
   	 * @param  [type] $products       [list product] has [id, quantity] id product and quantity product
   	 * @return
   	 */
   	private function makeExportProducts($products)
   	{
   		$user = User::find(Auth::id());
   		$inventory_ex = 0;
   		$quantity_ex = 0;
   		$amount_ex = 0;
   		$productsReal = [];
   		foreach ($products as $item) {
   			$product = Product::find($item['id']);
			if ($product && ($item['quantity'] > 0)) {
				$item['inventory'] = $product->prod_quantity - $product->count_sold - $item['quantity'];
				$inventory_ex += $item['inventory'];
				$quantity_ex += $item['quantity'];
				$amount_ex += $item['price'] * $item['quantity'];

				array_push($productsReal, $item);
			}
   		}
        if (count($productsReal) > 0) {
			$dataEx['user_id'] = $user->id;
			$dataEx['quantity_ex'] = $quantity_ex;
			$dataEx['inventory_ex'] = $inventory_ex;
			$dataEx['amount_ex'] = $amount_ex;
			$dataProdExp['export_id'] = (new ProductController)->createExport($user, $dataEx);
	   		foreach ($productsReal as $product) {
	   			$dataProdExp['prod_id'] = $product['id'];
	   			$dataProdExp['quantity_ex'] = $product['quantity'];
	   			$dataProdExp['price_ex'] = $product['price'];
	   			$dataProdExp['inventory_ex'] = $product['inventory'];
	   			(new ProductController)->createProductExport($dataProdExp);
	   		}
        }
   	}

   	/**
   	 * import again product
   	 * @param  [type] $products [list products import] [id, quantity] id and quantity of product
   	 * @return
   	 */
   	private function makeImportProducts($products)
   	{
		$user = User::find(Auth::id());
   		$inventory_imp = 0;
   		$quantity_imp = 0;
   		$amount_imp = 0;
   		$productsReal = [];
   		foreach ($products as $item) {
   			$product = Product::find($item['id']);
			if ($product && ($item['quantity'] > 0)) {
				$item['inventory'] = $product->prod_quantity - $product->count_sold;
				$item['count_sold'] = $product->count_sold;
				$item['parent_id'] = $product->parent_id;

				$inventory_imp += $item['inventory'];
				$quantity_imp += $item['quantity'];
				$amount_imp += $item['price'] * $item['quantity'];

				array_push($productsReal, $item);
			}
   		}
        if (count($productsReal) > 0) {
			$importId = (new ProductController)->createInfoImportProducts($user, $quantity_imp, $inventory_imp, $amount_imp, false);
	   		foreach ($productsReal as $product) {
	   			(new ProductController)->createProductsImport($product['id'], $importId, $product['quantity'], $product['price'], $product['inventory'], false);
	   			//down count sold product
	   			$dataUpdate['count_sold'] = $product['count_sold'] - $product['quantity'];
                (new ProductController)->updateProductCore($product['id'], $user, $dataUpdate);
                //down count sold for parent of product
                if ($product['parent_id']) {
                    $parent = Product::find($product['parent_id']);
                    if ($parent) {
                        $dataParent['count_sold'] = $parent->count_sold - $product['quantity'];
                        (new ProductController)->updateProductCore($parent->id, $user, $dataParent);
                    }
                }
	   		}
        }
   	}

   	private function refundProductsOfOrder($id)
   	{
        $order = Order::find($id);
        if ($order && $order->status_order != 'NEW') {
       		$prodsOrder = ProductOrder::where('order_id', $id)->get();
    		$user = User::find(Auth::id());
    		$inventory_imp = 0;
       		$quantity_imp = 0;
       		$amount_imp = 0;
       		$productsReal = [];
    		foreach ($prodsOrder as $prodItem) {
    			$product = Product::find($prodItem->prod_id);
    			if ($product) {
    				$inventory = $product->prod_quantity - $product->count_sold;
    				$inventory_imp += $inventory;
    				$quantity_imp += $prodItem->quantity;
    				$amount_imp += $prodItem->price_sale * $prodItem->quantity;

    				$prodItem->inventory = $inventory;
    				$prodItem->parent_id = $product->parent_id;
    				$prodItem->count_sold = $product->count_sold;
    				array_push($productsReal, $prodItem);
    			}
    		}

            if (count($productsReal) > 0) {
    			$importId = (new ProductController)->createInfoImportProducts($user, $quantity_imp, $inventory_imp, $amount_imp, false);
    			foreach ($productsReal as $product) {
    	   			(new ProductController)->createProductsImport($product->prod_id, $importId, $product->quantity, $product->price_sale, $product->inventory, false);

    	   			//down count sold product
    	   			$dataUpdate['count_sold'] = $product->count_sold - $product->quantity;
                    (new ProductController)->updateProductCore($product->prod_id, $user, $dataUpdate);
                    //down count sold for parent of product
                    if ($product->parent_id) {
                        $parent = Product::find($product->parent_id);
                        if ($parent) {
                            $dataParent['count_sold'] = $parent->count_sold - $product->quantity;
                            (new ProductController)->updateProductCore($parent->id, $user, $dataParent);
                        }
                    }
    			}
            }
        }
   	}

   	private function sentOrder($id)
   	{
   		$order = Auth::user()->ordersUser()->find($id);
   		if ($order) {
   			$dataOrder['id'] = $id;
   			$dataOrder['status_order'] = "SENT";
   			$this->updateCore($dataOrder);
   		}
   	}

   	private function refundingOrder($id)
   	{
   		$order = Auth::user()->ordersUser()->find($id);
   		if ($order) {
   			$this->refundProductsOfOrder($id);
   			$dataOrder['id'] = $id;
   			$dataOrder['status_order'] = "REFUNDING";
   			$this->updateCore($dataOrder);
   		}
   	}

   	private function refundedOrder($id)
   	{
   		$order = Auth::user()->ordersUser()->find($id);
   		if ($order) {
   			if ($order->time_refunding === null) {
   				$this->refundProductsOfOrder($id);
   			}
   			$dataOrder['id'] = $id;
   			$dataOrder['status_order'] = "REFUNDED";
   			$this->updateCore($dataOrder);
   		}
   	}

   	public function destroyOrder(Request $request)
   	{
   		$request->validate(
   			[
	   			'amount_dis' => 'required|numeric|min:0|max:99000000000',
	   			'order_id' => 'required|numeric'
   			],
   			[
   				'amount_dis.required' => 'Vui lòng nhập số tiền sẽ hoàn lại',
   				'amount_dis.numeric' => 'Vui lòng nhập chữ số',
   				'amount_dis.max' => 'Số tiền quá lớn',
   				'amount_dis.min' => 'Số tiền không được nhỏ hơn 0'
   			]
   		);
        $user = User::find(Auth::id());
   		$amountDis = $request->amount_dis;
   		$orderId = $request->order_id;
   		$order = $user->ordersUser()->find($orderId);
   		if ($order) {
   			$this->refundProductsOfOrder($orderId);

   			$dataOrder['id'] = $orderId;
   			$dataOrder['total_pay'] = floatval($order->total_pay) - floatval($amountDis);
   			$dataOrder['status_order'] = "CANCELED";
   			$this->updateCore($dataOrder);

            $dataPay['orderId'] = $orderId;
            $dataPay['amount'] = $amountDis;
            $dataPay['refund'] = true;
            $dataPay['customer_id'] = $order->cus_id;
            $this->createPayOrder($user, $dataPay);

            RollBackSaleData::dispatch($user, $order)->onQueue('statistic');

   			return response()->json([
   				'destroyed' => true
   			]);
   		}
   		return response()->json([
   			'message' => 'Không tìm thấy đon hàng'
   		], 302);
   	}

   	public function getListTransports(Request $request)
   	{
        $user = User::find(Auth::id());
        $code = $request->code;
        $typeDate = $request->checkDate == 'CREATE' ? 'created_at' : 'updated_at';
        $statusOrder = $request->statusOrder;
        $province = $request->province;
        $district = $request->district;
        $ward = $request->ward;
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $sortBy = $request->sortBy;

        $arrWhere = [
            ['orders.order_code', 'like', "%$code%"],
            ['orders.status_order', 'like', "%$statusOrder%"]
        ];
        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['transports.'.$typeDate, '>=', $timeFrom]);

        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['transports.'.$typeDate, '<=', $timeTo]);
        }
        if ($province) {
            array_push($arrWhere, ['orders.province_id', '=', $province]);
        }
        if ($district) {
            array_push($arrWhere, ['orders.district_id', '=', $district]);
        }
        if ($ward) {
            array_push($arrWhere, ['orders.ward_id', '=', $ward]);
        }
        if ($user->isAdmin() || $user->isManager()) {
            return DB::table('transports')
                        ->leftJoin('orders', 'orders.id', '=', 'transports.order_id')
                        ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                        ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                        ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                        ->select('transports.*', 'orders.order_code', 'orders.created_at as time_create_order', 'orders.total_amount', 'orders.status_order', 'orders.ad_receive', 'viet_nam_provinces.name', 'viet_nam_districts.name_district', 'viet_nam_wards.name_ward')
                        ->where('orders.user_id', $user->adminId())
                        ->whereIn('orders.status_order', ['CONFIRM', 'SENDING', 'SENT'])
                        ->where($arrWhere)
                        ->orderBy($typeDate, $sortBy)
                        ->paginate(10)
                        ->toJson();
        }
        return DB::table('transports')
                        ->leftJoin('orders', 'orders.id', '=', 'transports.order_id')
                        ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                        ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                        ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                        ->select('transports.*', 'orders.order_code', 'orders.created_at as time_create_order', 'orders.total_amount', 'orders.status_order', 'orders.ad_receive', 'viet_nam_provinces.name', 'viet_nam_districts.name_district', 'viet_nam_wards.name_ward')
                        ->where('orders.user_id', $user->adminId())
                        ->where('orders.staff_id', $user->id)
                        ->whereIn('orders.status_order', ['CONFIRM', 'SENDING', 'SENT'])
                        ->where($arrWhere)
                        ->orderBy($typeDate, $sortBy)
                        ->paginate(10)
                        ->toJson();
   	}

   	public function saveOrder(SaveOrderRequest $request)
   	{
   		$infoOrder = $request->infoOrder;
   		$products = $request->products;
        $infoOrder = $request->infoOrder;
    	$infoCus = $request->infoCus;
    	$productsDel = $request->productsDel;
    	$payments = $request->payments;
    	$transport = $request->transport;
        $user = User::find(Auth::id());
        $order = $user->ordersUser()->find($infoOrder['id']);
   		if ($order && ($infoOrder['status_order'] !== null) && ($infoOrder['status_order'] !== 'NEW')) {
	    	$exportMore = [];
			$refundProd = [];
	    	foreach ($products as $product) {
	    		$cur = ProductOrder::where('prod_id', $product['prod_id'])
	    									->where('order_id', $infoOrder['id'])
	    									->first();
                $prodInfo = Product::find($product['prod_id']);
	    		if ($cur && $prodInfo) {
	    			$diff = $product['quantity'] - $cur->quantity;
					$storage = $prodInfo->prod_quantity - $prodInfo->count_sold;
	    			if ($diff > 0) {
		    			if ($diff > $storage) {
		    				return response()->json([
		    					'message' => 'Sản phẩm ' . $prodInfo->prod_name . ' chỉ còn lại ' . $storage . ' sản phẩm trong kho'
		    				], 302);
		    			} else
		    			if ($diff < $storage) {
		    				$exportMore[] = [
		    					'id' => $product['prod_id'],
		    					'quantity' => $diff,
		    					'price' => $product['price_sale'],
		    				];
		    			}
	    			} else if ($diff < 0) {
	    				$refundProd[] = [
	    					'id' => $product['prod_id'],
	    					'quantity' => abs($diff),
	    					'price' => $product['price_sale'],
	    				];
	    			}
	    		} else if (!$cur && $prodInfo) {
					$storage = $prodInfo->prod_quantity - $prodInfo->count_sold;
                    if ($product['quantity'] > $storage) {
                        return response()->json([
                            'message' => 'Sản phẩm ' . $prodInfo->prod_name . ' chỉ còn lại ' . $storage . ' sản phẩm trong kho'
                        ], 302);
                    } else
                    if ($product['quantity'] < $storage) {
                        $exportMore[] = [
                            'id' => $product['prod_id'],
                            'quantity' => $product['quantity'],
                            'price' => $product['price_sale'],
                        ];
                    }
                }
	    	}
            $this->makeExportProducts($exportMore);
            //merge with product has delete
            $arrProd = [];
            foreach ($productsDel as $item) {
                $cur = ProductOrder::where('prod_id', $item['prod_id'])
                                            ->where('order_id', $infoOrder['id'])
                                            ->first();
                if ($cur && ($cur->quantity == $item['quantity'])) {
                    $check = true;
                    foreach ($refundProd as $el) {
                        if ($el['id'] == $item['prod_id']) {
                            $check = false;
                        }
                    }
                    if ($check) {
                        $tg = [];
                        $tg['id'] = $item['prod_id'];
                        $tg['price'] = $item['price'];
                        $tg['quantity'] = $item['quantity'];
                        $refundProd[] = $tg;
                    }
                }
            }
	    	$this->makeImportProducts($refundProd);
   		}

    	if (isset($transport['order_id'])) {
    		$this->updateTransportOrder($transport);
    	} else {
    		$this->createTransportOrder($user, $infoOrder['id'], $transport['amount'], $transport['note']);
    	}
    	$this->updateCore($infoOrder);
    	$this->updateInfoCus($infoCus);
    	$this->updateProductsOrder($products);
    	$this->removeProductsOfOrder($productsDel);
    	$this->updatePaymentsOrder($user, $order, $payments);
    	return response()->json([
    		'saved' => true
    	]);
   	}

    /**
     * check product in storage
     * @param  [type] $prodId   [id product]
     * @param  [type] $quantity [quantity product]
     * @return [boolean and string]           has or not has (with message error) product in storage 
     */
    public function checkProductInStorage($prodId, $quantity)
    {
        $product = Product::find($prodId);
        if ($product) {
            $storage = $product->prod_quantity - $product->count_sold;
            if (($quantity > 0) && ($storage > 0) && ($quantity <= $storage)) {
                return [
                    'hasStorage' => true
                ];
            }
            return [
                'hasStorage' => false,
                'message' => $product->prod_name . ' chỉ còn ' . $storage . ' sản phẩm trong kho'
            ];
        }
        return [
            'hasStorage' => false,
            'message' => 'Sản phẩm không tồn tại'
        ];
    }

    /**
     * check has address receive of order
     * @param  [type]  $id [id order]
     * @return boolean     has or not has address (with message)
     */
    public function hasAddressReceive($id)
    {
        $order = Order::find($id);
        if ($order && $order->province_id && $order->district_id && $order->ward_id) {
            return [
                'hasAddress' => true
            ];
        }
        return [
            'hasAddress' => false,
            'message' => 'Bạn cần thêm thông tin người nhận'
        ];
    }

    /**
     * quick change status of order
     * @param  [int]  $id      [id order]
     * @param  Request $request [status need change]
     * @return [boolean and string]          true or false (with message) 
     */
    public function quickUpdateOrder($id, Request $request)
    {
        $request->validate([
            'status' => 'required'
        ]);
        $user = User::find(Auth::id());
        $status = $request->status;
        $order = $user->ordersUser()->find($id);
        $dataUpdate['id'] = $id;
        if ($order) {
            $products = ProductOrder::where('order_id', $id)->get();
            switch ($status) {
                case 'CONFIRM':
                    $adReceive = $this->hasAddressReceive($id);
                    if (!$adReceive['hasAddress']) {
                        return response()->json([
                            'message' => $hasAddressReceive['message']
                        ], 302);
                    }

                    $transport = Transport::where('order_id', $id)->first();
                    if (!$transport) {
                        $this->createTransportOrder($user, $id, $order->total_amount, '');
                    }

                    
                    foreach ($products as $product) {
                        $storage = $this->checkProductInStorage($product->prod_id, $product->quantity);
                        if (!$storage['hasStorage']) {
                            return response()->json([
                                'message' => $storage['message']
                            ], 302);
                        }
                    }

                    $this->exportProductsOfOrder($id);
                    $dataUpdate['status_order'] = 'CONFIRM';
                    $this->updateCore($dataUpdate);
                    SaleStatistical::dispatch($user, $order)->onQueue('statistic');
                    break;
                case 'SENDING':
                    if ($order->time_confirmed === null) {

                        $adReceive = $this->hasAddressReceive($id);
                        if (!$adReceive['hasAddress']) {
                            return response()->json([
                                'message' => $hasAddressReceive['message']
                            ], 302);
                        }

                        $transport = Transport::where('order_id', $id)->first();
                        if (!$transport) {
                            $this->createTransportOrder($user, $id, $order->total_amount, '');
                        }

                        foreach ($products as $product) {
                            $storage = $this->checkProductInStorage($product->prod_id, $product->quantity);
                            if (!$storage['hasStorage']) {
                                return response()->json([
                                    'message' => $storage['message']
                                ], 302);
                            }
                        }
                        $this->exportProductsOfOrder($id);
                        SaleStatistical::dispatch($user, $order)->onQueue('statistic');
                    }
                    $dataUpdate['status_order'] = 'SENDING';
                    $this->updateCore($dataUpdate);
                    break;
                case 'SENT':

                    if (($order->time_confirmed === null) && ($order->time_sending === null)) {

                        $adReceive = $this->hasAddressReceive($id);
                        if (!$adReceive['hasAddress']) {
                            return response()->json([
                                'message' => $adReceive['message']
                            ], 302);
                        }

                        $transport = Transport::where('order_id', $id)->first();
                        if (!$transport) {
                            $this->createTransportOrder($user, $id, $order->total_amount, '');
                        }
                        
                        foreach ($products as $product) {
                            $storage = $this->checkProductInStorage($product->prod_id, $product->quantity);
                            if (!$storage['hasStorage']) {
                                return response()->json([
                                    'message' => $storage['message']
                                ], 302);
                            }
                        }

                        $this->exportProductsOfOrder($id);
                        SaleStatistical::dispatch($user, $order)->onQueue('statistic');
                    }

                    $dataUpdate['status_order'] = 'SENT';
                    $this->updateCore($dataUpdate);

                    $this->updateCampaignOfOrder($products);
                    break;
                case 'REFUNDING':
                    $this->refundProductsOfOrder($id);
                    $dataUpdate['status_order'] = 'REFUNDING';
                    $this->updateCore($dataUpdate);
                    $this->updateCampaignOfOrder($products, true);
                    RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
                    break;
                case 'REFUNDED':
                    if ($order->time_refunding === null) {
                        $this->refundProductsOfOrder($id);
                        $this->updateCampaignOfOrder($products, true);
                        RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
                    }

                    $dataUpdate['status_order'] = 'REFUNDED';
                    $this->updateCore($dataUpdate);
                    break;
                case 'CANCELED':
                    $request->validate(
                        [
                            'amount_dis' => 'required|numeric|min:0|max:99000000000'
                        ],
                        [
                            'amount_dis.min' => 'Số tiền không được nhỏ hơn 0',
                            'amount_dis.nax' => 'Số tiền quá lớn',
                            'amount_dis.max' => 'Số tiền phải là số'
                        ]
                    );
                    $amountDis = $request->amount_dis;

                    $this->refundProductsOfOrder($id);

                    $dataPay['orderId'] = $id;
                    $dataPay['amount'] = $amountDis;
                    $dataPay['refund'] = true;
                    $dataPay['customer_id'] = $order->cus_id;
                    $this->createPayOrder($user, $dataPay);

                    $totalPay = floatval($order->total_pay) - floatval($amountDis);
                    $dataUpdate['status_order'] = 'CANCELED';
                    $dataUpdate['total_pay'] = $totalPay;
                    $this->updateCore($dataUpdate);
                    if ($order->time_sent !== null) {
                        $this->updateCampaignOfOrder($products, true);
                    }
                    RollBackSaleData::dispatch($user, $order)->onQueue('statistic');
                    break;
                default:
                    break;
            }
    
            return response()->json([
                'updated' => true
            ]);
        }

        return response()->json([
            'message' =>'Không tồn tại đơn hàng'
        ], 302); 
    }

    public function updateCampaignOfOrder($products, $refund = false)
    {
        foreach ($products as $product) {
            if ($product->camp_id !== null) {
                $camp = Campaign::find($product->camp_id);
                $prod = Product::find($product->prod_id);
                if ($camp && $prod) {
                    $discount = ($camp->perc_disc * $prod->prod_price)/100;
                    if ($refund) {
                        $revenue = $camp->revenue - ($prod->prod_price - $discount) * $product->quantity;
                        $count_sell = $camp->count_sell - $product->quantity;
                        $spent_money = $camp->spent_money - $discount * $product->quantity;
                    } else {
                        $revenue = ($prod->prod_price - $discount) * $product->quantity + $camp->revenue;
                        $count_sell = $product->quantity + $camp->count_sell;
                        $spent_money = $discount * $product->quantity + $camp->spent_money;
                    }

                    $camp->update([
                        'count_sell' => $count_sell,
                        'revenue' => round($revenue, 4),
                        'spent_money' => round($spent_money, 4)
                    ]);
                }
            }
        }
    }

    public function countNewOrder()
    {
        $count = Auth::user()->ordersUser()->where('status_order', 'NEW')->count();
        return response()->json([
            'count_new' => $count
        ]);
    }

    public function exportTransportList($type)
    {
        $user = User::find(Auth::id());
        $transports = DB::table('transports')
                            ->leftJoin('orders', 'orders.id', '=', 'transports.order_id')
                            ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                            ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                            ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                            ->select('transports.*', 'orders.order_code', 'orders.created_at as time_create_order', 'orders.total_amount', 'orders.status_order', 'orders.ad_receive', 'viet_nam_provinces.name', 'viet_nam_districts.name_district', 'viet_nam_wards.name_ward')
                            ->where('orders.user_id', $user->adminId())
                            ->whereIn('orders.status_order', ['CONFIRM', 'SENDING', 'SENT'])
                            ->get()
                            ->toArray();
        $transports = json_decode(json_encode($transports), true);
        $header =  ['ID đơn vận', 'Ghi chú', 'Tổng tiền cần thu', 'ID đơn', 'Thời gian tạo yêu cầu', 'Thời gian cập nhật gần nhất', '', '','Mã đơn', 'Thời gian tạo đơn', 'Tổng tiền cần thanh toán', 'Trạng thái đơn', 'Địa chỉ', 'Tỉnh/TP', 'Quận/Huyện', 'Xã/Phường'];
        array_unshift($transports, $header);

        return \Excel::create('transports_list', function($excel) use ($transports) {

            $excel->sheet('transports', function($sheet) use ($transports)

            {

                $sheet->fromArray($transports, null, 'A1', false, false);

            });

        })->download($type);
    }
}
