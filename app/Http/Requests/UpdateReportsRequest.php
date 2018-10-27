<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportsRequest extends FormRequest
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
            'reports' => 'required|array',
            'reports.*.id' => 'required|numeric|min:1',
            'reports.*.name_cus' => 'nullable|max:100',
            'reports.*.phone_cus' => ['nullable', 'regex: /'.REGEX_PHONE.'/'],
            'reports.*.des_report' => 'required|max:190'
        ];
    }

    public function messages()
    {
        return [
            'reports.required' => 'Vui lòng chọn danh sách đen',
            'reports.*.phone_cus.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động',
            'reports.*.des_report.required' => 'Vui lòng không bỏ trống nội dung báo xấu'
        ];
    }
}
