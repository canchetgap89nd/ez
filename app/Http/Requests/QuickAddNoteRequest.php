<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuickAddNoteRequest extends FormRequest
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
            'note_id' => 'nullable|numeric|min:1',
            'note_content' => 'required|max:190',
            'name_cus' => 'required|max:190',
            'fb_id_cus' => 'required|numeric|min:1',
            'fb_page_id' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'note_content.required' => 'Vui lòng nhập nội dung ghi chú',
            'note_content.max' => 'Ghi chú quá dài',
            'fb_id_cus.required' => 'Vui lòng chọn khách hàng',
            'name_cus.required' => 'Vui lòng nhập tên khách hàng',
            'name_cus.max' => 'Tên khách hàng quá dài',
        ];
    }
}
