<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPaymentRequest extends FormRequest
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
            'tax' => 'required|numeric|min:0',
            'other_payment' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'paid' => 'boolean|required',
            'duration' => 'numeric|required|min:1',
            'duration_bonus' => 'required|numeric|min:0',
        ];
    }
}
