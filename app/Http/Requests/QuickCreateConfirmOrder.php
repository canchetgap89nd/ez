<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuickCreateConfirmOrder extends FormRequest
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
            'infoCus.id' => 'nullable|numeric',
            'infoCus.fb_id_cus' => 'required|numeric',
            'infoCus.name_cus' => 'required|max:190',
            'infoCus.email_cus' => 'nullable|email|max:190',
            'infoCus.address_cus' => 'nullable|max:190',
            'infoOrder.discount' => 'required|numeric|max:99000000000|min:0',
            'infoOrder.totalValue' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.valueHasSale' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.shipFee' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.totalAmount' => 'required|numeric|min:0|max:99000000000',
            'infoOrder.note' => 'nullable|max:190',
            'infoOrder.addressReceive' => 'nullable|max:190',
            'infoOrder.nameReceive' => 'required|max:190',
            'infoOrder.phoneReceive' => ['regex:/^(01[2689]|09)[0-9]{8}$/', 'required'],
            'infoOrder.provinceId' => 'required|numeric',
            'infoOrder.districtId' => 'required|numeric',
            'infoOrder.wardId' => 'required|numeric',
            'fbPageId' => 'required|numeric',
            'products' => 'required|array',
            'products.*' => 'required|array',
            'products.*.id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric',
            'products.*.price' => 'required|numeric',
            'products.*.priceSale' => 'required|numeric',
            'products.*.campId' => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
            'infoOrder.phoneReceive.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động',
            'infoOrder.phoneReceive.required' => 'Vui lòng nhập số điện thoại di động người nhận',
            'infoOrder.nameReceive.required' => 'Vui lòng nhập tên người nhận',
            'infoOrder.nameReceive.max' => 'Tên người nhận quá dài',
            'infoOrder.addressReceive.max' => 'Địa chỉ người nhận quá dài',
            'infoOrder.note.max' => 'Ghi chú quá dài',
            'infoOrder.totalAmount.max' => 'Giá trị tiền quá lớn',
            'infoOrder.valueHasSale.max' => 'Giá trị tiền quá lớn',
            'infoOrder.shipFee.max' => 'Giá trị tiền quá lớn',
            'infoOrder.totalValue.max' => 'Giá trị tiền quá lớn',
            'infoOrder.discount.max' => 'Giá trị tiền quá lớn',
            'infoCus.fb_id_cus.required' => 'Vui lòng chọn một khách hàng',
            'products.required' => 'Vui lòng chọn sản phẩm',
            'infoCus.name_cus.required' => 'Vui lòng nhập tên khách hàng',
            'infoCus.name_cus.max' => 'Tên khách hàng quá dài',
            'infoOrder.provinceId.required' => 'Vui lòng chọn địa chỉ người nhận',
            'infoOrder.districtId.required' => 'Vui lòng chọn địa chỉ người nhận',
            'infoOrder.wardId.required' => 'Vui lòng chọn địa chỉ người nhận'
        ];
    }
}
