<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Admin\PackageAndPayment\UserPayment;
use App\models\Admin\PackageAndPayment\UserPackage;

class UserPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $payId = $request->paymentId;
        $payment = UserPayment::with('user', 'package')->find($payId);
        if ($payment && !$payment->is_active) {
            $userPack = UserPackage::where('user_id', $payment->user_id)
                                    ->where('package_id', $payment->package_id)
                                    ->first();
            return view('admin.userpack.create', compact('payment', 'userPack'));
        }
        return redirect()->route('payment.index')->with([
            'status' => 'danger',
            'message' => 'Can not find order'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payId = $request->payment_id;
        $payment = UserPayment::find($payId);
        if ($payment) {
            $userPack = UserPackage::where('user_id', $payment->user_id)
                                    ->where('package_id', $payment->package_id)
                                    ->first();
            $totalMonth = $payment->duration + $payment->duration_bonus;
            if (empty($userPack)) {
                $expire_at = date('Y-m-d H:i:s', strtotime('+ ' . $totalMonth . ' months', time()));
                UserPackage::create([
                    'user_id' => $payment->user_id,
                    'package_id' => $payment->package_id,
                    'expire_at' => $expire_at
                ]);
            } else {
                $time_has = strtotime($userPack->expire_at);
                $expire_at = date('Y-m-d H:i:s', strtotime('+ ' . $totalMonth . ' months', $time_has));
                $userPack->update([
                    'expire_at' => $expire_at
                ]);
            }
            $payment->update(['is_active' => true]);

            return redirect()->route('payment.index')->with([
                'status' => 'success',
                'message' => 'Package for account '. $payment->user_id .' is active!'
            ]);
        }
        return redirect()->route('payment.index')->with([
            'status' => 'danger',
            'message' => 'Can not find order'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
