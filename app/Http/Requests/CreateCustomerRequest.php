<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
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
            'name_cus' => 'required|max:190',
            'phone_cus' => ['nullable', 'regex:/'.REGEX_PHONE.'/'],
            'email_cus' => 'nullable|email|max:190',
            'address_cus' => 'nullable|max:190',
            'groups' => 'nullable|array',
            'groups.*.id' => 'numeric|min:0',
            'notes' => 'nullable|array',
            'notes.*.note_content' => 'max:190'
        ];
    }

    public function messages()
    {
        return [
            'name_cus.required' => 'Vui lòng nhập tên khách hàng',
            'name_cus.max' => 'Tên khách hàng quá dài',
            'address_cus.max' => 'Địa chỉ quá dài',
            'phone_cus.required' => 'Vui lòng nhập số điện thoại khách hàng',
            'phone_cus.regex' => 'Vui lòng nhập đúng định dạng số điện thoại di động. Ví dụ: 090..., 016...',
            'email_cus.email' => 'Vui lòng nhập đúng định dạng email',
            'email_cus.max' => 'Địa chỉ email quá dài',
            'notes.*.note_content.max' => 'Ghi chú quá dài'
        ];
    }
}
