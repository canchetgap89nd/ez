<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductsRequest extends FormRequest
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
            'products' => 'required',
            'products.*.quantity_import' => 'required|numeric|min:1|max:99000000',
            'products.*.priceImp' => 'required|numeric|min:0|max:99000000000',
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'Vui lòng chọn sản phẩm',
            'products.*.quantity_import.required' => 'Vui lòng nhập số lượng nhập vào',
            'products.*.quantity_import.numeric' => 'Số lượng sản phẩm sai định dạng',
            'products.*.quantity_import.min' => 'Số lượng sản phẩm phải lớn hơn 0',
            'products.*.quantity_import.max' => 'Số lượng sản phẩm vượt quá giới hạn cho phép',
            'products.*.priceImp.required' => 'Vui lòng điền giá nhập của sản phẩm', 
            'products.*.priceImp.numeric' => 'Giá nhập của sản phẩm phải là số', 
            'products.*.priceImp.min' => 'Giá nhập của sản phẩm không được nhỏ hơn 0',
            'products.*.priceImp.max' => 'Giá nhập của sản phẩm vượt quá giới hạn cho phép',
        ];
    }
}
