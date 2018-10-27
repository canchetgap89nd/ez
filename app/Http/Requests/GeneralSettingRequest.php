<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
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
            'hide_all_cmt' => 'required|boolean',
            'hide_cmt_has_phone' => 'required|boolean',
            'hide_cmt_has_key' => 'required|boolean',
            'auto_like' => 'required|boolean',
            'ping_notify' => 'required|boolean',
            'priority_cmt_has_key' => 'required|boolean',
            'filter_email' => 'required|boolean',
            'filter_phone' => 'required|boolean',
            'key_cmt_hide' => 'array',
            'key_cmt_hide.*' => 'max:50',
            'key_cmt_priority' => 'array',
            'key_cmt_priority' => 'max:50'
        ];
    }
}
