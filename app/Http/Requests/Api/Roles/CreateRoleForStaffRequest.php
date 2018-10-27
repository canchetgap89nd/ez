<?php

namespace App\Http\Requests\Api\Roles;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleForStaffRequest extends FormRequest
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
            'role_staff' => 'required|in:1,2,3'
        ];
    }

    public function messages()
    {
        return [
            'role_staff.required' => 'Vui lòng chọn quyền cho nhân viên',
            'role_staff.in' => 'Vui lòng chọn đúng quyền cho nhân viên',
        ];
    }
}
