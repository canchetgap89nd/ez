<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExportRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*.prod_id' => 'required|numeric',
            'products.*.quantity_ex' => 'required|min:0|max:99000000000|numeric',
            'products.*.price_ex' => 'required|min:0|max:99000000000|numeric',
            'products.*.inventory_ex' => 'required|min:0|max:99000000000|numeric',
            'infoExport' => 'required|array',
            'infoExport.quantity_ex' => 'required|min:0|max:99000000000|numeric',
            'infoExport.inventory_ex' => 'required|min:0|max:99000000000|numeric',
            'infoExport.amount_ex' => 'required|min:0|max:99000000000|numeric'
        ];
    }

    public function messages() 
    {
        return [
            'products.required' => 'Vui lòng chọn sản phẩm',
            'products.*.quantity_ex.required' => 'Vui lòng nhập số lượng sản phẩm',
            'products.*.quantity_ex.min' => 'Số lượng sản phẩm không được nhỏ hơn 0',
            'products.*.quantity_ex.max' => 'Số lượng sản phẩm quá lớn',
            'products.*.quantity_ex.numeric' => 'Vui lòng nhập chữ số cho số lượng sản phẩm',
            'products.*.price_ex.numeric' => 'Vui lòng nhập chữ số cho giá sản phẩm',
            'products.*.price_ex.min' => 'Giá sản phẩm không được nhỏ hơn 0',
            'products.*.price_ex.max' => 'Giá sản phẩm quá lớn',
            'products.*.price_ex.required' => 'Vui lòng nhập giá sản phẩm',
            'infoExport.required' => 'Vui lòng nhập thông tin'
        ];
    }
}
