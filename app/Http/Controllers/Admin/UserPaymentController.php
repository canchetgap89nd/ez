<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Admin\PackageAndPayment\UserPayment;
use App\models\Admin\PackageAndPayment\Package;
use App\Http\Requests\Admin\UpdateUserPaymentRequest;
use Auth;
use App\Http\Requests\Admin\CreateUserPaymentRequest;
use App\User;
use App\Traits\GenerateTrait;

class UserPaymentController extends Controller
{
    use GenerateTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payments = UserPayment::with('user', 'package', 'staff')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.payment.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $packages = Package::all();
        return view('admin.payment.create', compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserPaymentRequest $request)
    {
        $packageId = $request->package_id;
        $userId = $request->user_id;
        $package = Package::find($packageId);
        $user = User::find($userId);
        if ($package && $user) {
            $price = $request->price;
            $duration = $request->duration;
            $duration_bonus = $request->duration_bonus;
            $discount = $request->discount;
            $other_payment = $request->other_payment;
            $tax = $request->tax;
            $paid = $request->paid;

            $amount = $price * $duration;
            $totalPay = $amount + $other_payment + $tax;
            $totalAfterDiscount = $totalPay - $discount;

            UserPayment::create([
                'pay_code' => $this->generatePayCode(),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'amount' => $amount,
                'tax' => $tax,
                'other_payment' => $other_payment,
                'price' => $price,
                'duration' => $duration,
                'duration_bonus' => $duration_bonus,
                'discount' => $discount,
                'total_payable' => $totalPay,
                'total_after_discount' => $totalAfterDiscount,
                'paid' => $paid,
                'admin_id' => Auth::guard('admin')->id()
            ]);
            return redirect()->route('payment.index')->with([
                'status' => 'success',
                'message' => 'Create Success!'
            ]);
        }
        return redirect()->route('payment.index')->with([
            'status' => 'danger',
            'message' => 'Can not find object'
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
        $payment = UserPayment::with('user', 'package', 'staff')->find($id);
        return view('admin.payment.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserPaymentRequest $request, $id)
    {
        $payment = UserPayment::find($id);
        if ($payment) {
            $price = $request->price;
            $duration = $request->duration;
            $duration_bonus = $request->duration_bonus;
            $discount = $request->discount;
            $other_payment = $request->other_payment;
            $tax = $request->tax;
            $paid = $request->paid;

            $amount = $price * $duration;
            $totalPay = $amount + $other_payment + $tax;
            $totalAfterDiscount = $totalPay - $discount;
            $payment->update([
                'amount' => $amount,
                'tax' => $tax,
                'other_payment' => $other_payment,
                'price' => $price,
                'duration' => $duration,
                'duration_bonus' => $duration_bonus,
                'discount' => $discount,
                'total_payable' => $totalPay,
                'total_after_discount' => $totalAfterDiscount,
                'paid' => $paid,
                'admin_id' => Auth::guard('admin')->id()
            ]);
            return redirect()->route('payment.index')->with([
                'status' => 'success',
                'message' => 'Update Success!'
            ]);
        }
        return redirect()->route('payment.index')->with([
            'status' => 'danger',
            'message' => 'Cannot find order!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = UserPayment::find($id);
        if ($payment) {
            $payment->delete();
            return redirect()->route('payment.index')->with([
                'status' => 'success',
                'message' => 'Delete Success!'
            ]);
        }
        return redirect()->route('payment.index')->with([
            'status' => 'danger',
            'message' => 'Cannot find order!'
        ]);
    }
}
