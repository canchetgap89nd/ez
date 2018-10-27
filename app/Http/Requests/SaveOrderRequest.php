<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends FormRequest
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
            'status' => 'required',
            'infoCus' => 'required|array',
            'infoCus.id' => 'required|numeric',
            'infoCus.name_cus' => 'required|max:190',
            'infoCus.phone_cus' => ['regex:/^(01[2689]|09)[0-9]{8}$/', 'nullable'],
            'infoCus.address_cus' => 'nullable|max:190',
            'infoCus.email_cus' => 'nullable|email|max:190',
            'infoOrder.id' => 'required|numeric',
            'infoOrder.discount' => 'required|numeric|max:99000000000|min:0',
            'infoOrder.total_value' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.value_has_sale' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.ship_fee' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.total_amount' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.total_pay' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.note_order' => 'nullable|max:190',
            'infoOrder.ad_receive' => 'nullable|max:190',
            'infoOrder.name_receive' => 'required|max:190',
            'infoOrder.email_receive' => 'nullable|email|max:190',
            'infoOrder.phone_receive' => ['regex:/^(01[2689]|09)[0-9]{8}$/', 'nullable'],
            'products' => 'required|array',
            'products.*' => 'required|array',
            'products.*.prod_id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric',
            'products.*.price' => 'required|numeric',
            'products.*.price_sale' => 'required|numeric',
            'products.*.camp_id' => 'nullable|numeric',
            'infoOrder.province_id' => 'nullable|numeric',
            'infoOrder.district_id' => 'nullable|numeric',
            'infoOrder.ward_id' => 'nullable|numeric',
            'payments' => 'nullable|array',
            'payments.*.amount_pay' => 'numeric|max:99000000000|min:0',
            'transport' => 'required|array',
            'transport.amount' => 'required|numeric|max:99000000000|min:0',
            'transport.note' => 'nullable|max:190',
        ];
    }

    public function messages()
    {
        return [
            'infoOrder.phone_receive.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động',
            'infoOrder.name_receive.required' => 'Vui lòng nhập tên người nhận',
            'infoOrder.name_receive.max' => 'Tên người nhận quá dài',
            'infoOrder.address_receive.max' => 'Địa chỉ người nhận quá dài',
            'infoOrder.email_receive.max' => 'Địa chỉ email quá dài',
            'infoOrder.email_receive.email' => 'Vui lòng nhập đúng định dạng email',
            'infoOrder.note.max' => 'Ghi chú quá dài',
            'infoOrder.total_amount.max' => 'Giá trị tiền quá lớn',
            'infoOrder.ship_fee.max' => 'Giá trị tiền quá lớn',
            'infoOrder.total_value.max' => 'Giá trị tiền quá lớn',
            'infoOrder.value_has_sale.max' => 'Giá trị tiền quá lớn',
            'infoOrder.discount.max' => 'Giá trị tiền quá lớn',
            'products.required' => 'Vui lòng chọn sản phẩm',
            'infoCus.required' => 'Vui lòng nhập thông tin khách hàng',
            'infoCus.name_cus.required' => 'Vui lòng nhập tên khách hàng',
            'infoCus.name_cus.max' => 'Tên khách hàng quá dài',
            'infoCus.phone_cus.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động',
            'infoCus.address_cus.max' => 'Địa chỉ khách hàng quá dài',
            'infoCus.email_cus.max' => 'Địa chỉ email quá dài',
            'infoCus.email_cus.email' => 'Vui lòng nhập đúng định dạng email',
            'payments.*.amount_pay.max' => 'Số tiền thanh toán quá lớn',
            'payments.*.amount_pay.min' => 'Số tiền thanh toán không được nhỏ hơn 0',
            'transport.required' => 'Vui lòng nhập thông tin giao hàng',
            'transport.amount.numeric' => 'Số tiền phải là số',
            'transport.amount.max' => 'Số tiền quá lớn',
            'transport.amount.required' => 'Vui lòng nhập số tiền giao hàng',
            'transport.amount.min' => 'Số tiền không được nhỏ hơn 0',
            'transport.note.max' => 'Ghi chú quá dài',
        ];
    }
}
