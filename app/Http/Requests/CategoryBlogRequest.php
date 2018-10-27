<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryBlogRequest extends FormRequest
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
            'cate_name' => 'required|max:190',
            'cate_keyword' => 'nullable|max:190',
            'cate_slug' => [
                'required',
                'max:190',
                Rule::unique('slug_blogs', 'slug')->ignore($this->get('id'), 'entity_id')->where(function($query) {
                    $query->where('entity_type', ENTITY_CATE);
                })
            ],
            'cate_desc' => 'nullable|max:190',
            'cate_order' => 'nullable|numeric',
            'cate_active' => 'required|in:1,0'
        ];
    }
}
