<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlackCustomerRequest extends FormRequest
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
            'title_report' => 'required|max:100',
            'des_report' => 'required|max:190',
            'name_cus' => 'nullable|max:50',
            'phone_cus' => ['nullable','regex:/'.REGEX_PHONE.'/'],
            'email_cus' => 'nullable|email|max:50',
            'fb_id_cus' => 'nullable|min:1|numeric'
        ];
    }

    public function messages()
    {
        return [
            'title_report.required' => 'Vui lòng nhập tiêu đề',
            'title_report.max' => 'Tiêu đề quá dài',
            'des_report.required' => 'Vui lòng nhập nội dung báo cáo',
            'des_report.max' => 'Nội dung báo cáo quá dài',
            'phone_cus.regex' => 'Sai định dạng số điện thoại di động',
            'email_cus.email' => 'Sai định dạng email',
            'email_cus.max' => 'Email quá dài'
        ];
    }
}
