<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCampaignRequest extends FormRequest
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
            'name' => 'required|max:190',
            'discount' => 'required|numeric|min:0|max:100',
            'startTime' => 'nullable|date_format:Y-m-d H:i:s',
            'endTime' => 'nullable|date_format:Y-m-d H:i:s',
            'typeTime' => 'required|numeric|in:1,2',
            'products' => 'required|array',
            'products.*' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên khuyến mãi',
            'name.max' => 'Tên khuyến mãi quá dài',
            'discount.required' => 'Vui lòng nhập mức giảm giá',
            'discount.numeric' => 'Mức khuyến mãi phải là số',
            'discount.max' => 'Giá trị tối đa của mức giảm giá là 100',
            'discount.min' => 'Mức giảm giá không được nhỏ hơn 0',
            'products.required' => 'Vui lòng nhập sản phẩm cho khuyến mãi'
        ];
    }
}
