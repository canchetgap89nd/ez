<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class CreateUserInfoRequest extends FormRequest
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
        if (Auth::user()->isWare()) {
            return [
                'phone' => ['regex:/'. REGEX_PHONE .'/', 'required'],
                'email' => 'nullable|email',
                'name' => 'required'
            ];
        }
        return [
            'pages' => 'required',
            'phone' => ['regex:/'. REGEX_PHONE .'/', 'required'],
            'email' => 'nullable|email',
            'name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'pages.required' => 'Bạn cần chọn ít nhất 1 Fanpage',
            'phone.required' => 'Bạn cần nhập vào số điện thoại của bạn',
            'phone.regex' => 'Bạn cần nhập vào số điện thoại di động',
            'email.required' => 'Vui lòng thêm email',
            'email.email' => 'Vui lòng điền đúng định dạng email Ví dụ: dantruong@gmail.com...',
            'name.required' => 'Vui lòng điền họ và tên của bạn',
        ];
    }
}
