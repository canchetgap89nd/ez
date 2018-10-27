<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePackageRequest extends FormRequest
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
            'name' => [
                'regex: /[A-Z][A-Z0-9_]+/', 
                'required', 
                Rule::unique('packages', 'name')->ignore($this->get('id'))
            ],
            'display_name' => 'required|max:190',
            'features' => 'required|array',
            'price' => 'required|numeric|min:0',
            'page_limit' => 'required|numeric|min:1',
            'staff_limit' => 'required|numeric|min:1',
        ];
    }
}
