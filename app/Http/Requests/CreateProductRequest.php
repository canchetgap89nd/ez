<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;

class CreateProductRequest extends FormRequest
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
        $rules =  [
            'product.name' => 'required|max:191',
            'product.cate' => 'required|numeric|min:1|max:99000000',
            'product.price' => 'nullable|numeric|min:0|max:99000000000',
            'product.quantity' => 'nullable|numeric|min:0|max:99000000',
            'product.thumb' => 'required|max:500',
            'childs.*' => 'nullable',
            'childs.*.props' => 'required|array',
            'childs.*.props.*' => 'required|numeric',
            'childs.*.quantity' => 'nullable|numeric|min:0|max:99000000',
            'childs.*.price' => 'required|numeric|min:0|max:99000000000',
            'childs.*.priceImp' => 'required|numeric|min:0|max:99000000000',
            'childs.*.lastImg' => 'nullable|max:500',
            'childs.*.images' => 'nullable|array',
            'childs.*.images.*' => 'nullable|max:500',
        ];
        $childs = Request::input('childs.*.code');
        $ruleCodeParent =   [
                                'regex:/^[a-zA-Z0-9]*$/',
                                Rule::unique('products', 'prod_code')->ignore(Request::input('product.id'))->where(function ($query) {
                                    return $query->where('user_id', Auth::id());
                                }),
                                'max:10',
                                'nullable',
                            ];
        foreach ($childs as $k1 => $value1) {
            $ruleCodeParent[] = 'different:childs.' . $k1 . '.code';
            $ruleCodeChild =    [
                                'regex:/^[a-zA-Z0-9]*$/',
                                'max:10',
                                Rule::unique('products', 'prod_code')->where(function ($query) {
                                    return $query->where('user_id', Auth::id());
                                }),
                                'nullable'
                            ];
            foreach ($childs as $k2 => $value2) {
                if ($k1 !== $k2) {
                    $ruleCodeChild[] = 'different:childs.' . $k2 . '.code';
                }
            }
            $rules['childs.' . $k1 . '.code'] = $ruleCodeChild;
        }
        $rules['product.code'] = $ruleCodeParent;

        return $rules;
    }

    public function messages()
    {
        return [
            'product.name.required' => 'Vui lòng nhập tên sản phẩm',
            'product.code.regex' => 'Mã sản phẩm phải bao gồm chữ in hoa và số',
            'product.code.unique' => 'Mã sản phẩm đã tồn tại',
            'product.code.different' => 'Mã các sản phẩm không được trùng nhau',
            'product.code.max' => 'Mã các sản phẩm không được nhiều hơn 10 ký tự',
            'product.cate.required' => 'Vui lòng chọn danh mục cho sản phẩm',
            'product.cate.numeric' => 'Vui lòng chọn danh mục sản phẩm',
            'product.price.required' => 'Vui lòng nhập giá cho sản phẩm',
            'product.price.numeric' => 'Nhập chữ số cho giá cho sản phẩm',
            'product.price.min' => 'Giá của sản phẩm không được nhỏ hơn 0',
            'product.price.max' =>  'Giá của sản phẩm quá lớn',
            'product.priceImp.required' => 'Vui lòng điền giá nhập cho sản phẩm',
            'product.priceImp.numeric' => 'Nhập chữ số cho giá nhập cho sản phẩm',
            'product.priceImp.min' => 'Giá nhập của sản phẩm không được nhỏ hơn 0',
            'product.priceImp.max' =>  'Giá nhập của sản phẩm quá lớn',
            'product.quantity.numeric' => 'Nhập chữ số cho số lượng sản phẩm',
            'product.quantity.min' => 'Số lượng sản phẩm không được nhỏ hơn 0',
            'product.quantity.max' => 'Số lượng sản phẩm quá lớn',
            'product.thumb.required' => 'Vui lòng thêm ảnh đại diện cho sản phẩm',
            'childs.*.code.regex' => 'Mã sản phẩm phải bao gồm chữ in hoa và số',
            'childs.*.code.unique' => 'Mã sản phẩm đã tồn tại',
            'childs.*.code.different' => 'Mã các sản phẩm không được trùng nhau',
            'childs.*.code.max' => 'Mã các sản phẩm không được nhiều hơn 10 ký tự',
            'childs.*.props.required' => 'Hãy chọn ít nhất một thuộc tính cho sản phẩm',
            'childs.*.props.array' => 'Sai định dạng thuộc tính cho sản phẩm',
            'childs.*.quantity.numeric' => 'Nhập chữ số cho số lượng sản phẩm',
            'childs.*.quantity.min' => 'Số lượng sản phẩm không được nhỏ hơn 0',
            'childs.*.quantity.max' => 'Số lượng sản phẩm quá lớn',
            'childs.*.price.numeric' => 'Nhập chữ số cho giá sản phẩm',
            'childs.*.price.required' => 'Vui lòng nhập giá cho sản phẩm',
            'childs.*.price.min' => 'Giá của sản phẩm không được nhỏ hơn 0',
            'childs.*.price.max' => 'Giá của sản phẩm quá lớn',
            'childs.*.priceImp.numeric' => 'Nhập chữ số cho giá nhập sản phẩm',
            'childs.*.priceImp.required' => 'Vui lòng điền giá nhập cho sản phẩm',
            'childs.*.priceImp.min' => 'giá nhập của sản phẩm không được nhỏ hơn 0',
            'childs.*.priceImp.max' => 'giá nhập của sản phẩm quá lớn',
        ];
    }
}
