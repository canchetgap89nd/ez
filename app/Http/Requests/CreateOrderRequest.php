<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'infoCus' => 'required|array',
            'infoCus.name' => 'required|max:190',
            'infoCus.phone' => ['regex:/^(01[2689]|09)[0-9]{8}$/', 'nullable'],
            'infoCus.address' => 'nullable|max:190',
            'infoCus.email' => 'nullable|email|max:190',
            'infoOrder.discount' => 'required|numeric|max:99000000000|min:0',
            'infoOrder.totalValue' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.valueHasSale' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.shipFee' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.totalAmount' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.totalPay' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.note' => 'nullable|max:190',
            'infoOrder.addressReceive' => 'nullable|max:190',
            'infoOrder.nameReceive' => 'required|max:190',
            'infoOrder.emailReceive' => 'nullable|email|max:190',
            'infoOrder.phoneReceive' => ['regex:/^(01[2689]|09)[0-9]{8}$/', 'nullable'],
            'products' => 'required|array',
            'products.*' => 'required|array',
            'products.*.id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric',
            'products.*.price' => 'required|numeric',
            'products.*.priceSale' => 'required|numeric',
            'products.*.campId' => 'nullable|numeric',
            'infoOrder.provinceId' => 'nullable|numeric',
            'infoOrder.districtId' => 'nullable|numeric',
            'infoOrder.wardId' => 'nullable|numeric',
            'payments' => 'nullable|array',
            'payments.*.amount' => 'numeric|max:99000000000|min:0',
            'transport' => 'required|array',
            'transport.amount' => 'required|numeric|max:99000000000|min:0',
            'transport.note' => 'nullable|max:190',
        ];
    }

    public function messages()
    {
        return [
            'infoOrder.phoneReceive.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động',
            'infoOrder.nameReceive.required' => 'Vui lòng nhập tên người nhận',
            'infoOrder.nameReceive.max' => 'Tên người nhận quá dài',
            'infoOrder.addressReceive.max' => 'Địa chỉ người nhận quá dài',
            'infoOrder.emailReceive.max' => 'Địa chỉ email quá dài',
            'infoOrder.emailReceive.email' => 'Vui lòng nhập đúng định dạng email',
            'infoOrder.note.max' => 'Ghi chú quá dài',
            'infoOrder.totalAmount.max' => 'Giá trị tiền quá lớn',
            'infoOrder.shipFee.max' => 'Giá trị tiền quá lớn',
            'infoOrder.totalValue.max' => 'Giá trị tiền quá lớn',
            'infoOrder.valueHasSale.max' => 'Giá trị tiền quá lớn',
            'infoOrder.discount.max' => 'Giá trị tiền quá lớn',
            'products.required' => 'Vui lòng chọn sản phẩm',
            'infoCus.required' => 'Vui lòng nhập thông tin khách hàng',
            'infoCus.name.required' => 'Vui lòng nhập tên khách hàng',
            'infoCus.name.max' => 'Tên khách hàng quá dài',
            'infoCus.phone.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động',
            'infoCus.address.max' => 'Địa chỉ khách hàng quá dài',
            'infoCus.email.max' => 'Địa chỉ email quá dài',
            'infoCus.email.email' => 'Vui lòng nhập đúng định dạng email',
            'payments.*.amount.max' => 'Số tiền thanh toán quá lớn',
            'payments.*.amount.min' => 'Số tiền thanh toán không được nhỏ hơn 0',
            'transport.required' => 'Vui lòng nhập thông tin giao hàng',
            'transport.amount.numeric' => 'Số tiền phải là số',
            'transport.amount.max' => 'Số tiền quá lớn',
            'transport.amount.required' => 'Vui lòng nhập số tiền giao hàng',
            'transport.amount.min' => 'Số tiền không được nhỏ hơn 0',
            'transport.note.max' => 'Ghi chú quá dài',
        ];
    }
}
