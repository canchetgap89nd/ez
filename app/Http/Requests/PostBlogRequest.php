<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostBlogRequest extends FormRequest
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
            'postTitle' => 'required|max:190',
            'post_keyword' => 'nullable|max:190',
            'postDesc' => 'required|max:1000',
            'postSlug' => [
                'required',
                'max:190',
                Rule::unique('slug_blogs', 'slug')->ignore($this->get('id'), 'entity_id')->where(function($query) {
                    $query->where('entity_type', ENTITY_POST);
                })
            ],
            'postContent' => 'required',
            'postCate' => 'required',
            'postThumb' => 'required',
            'postSchedule' => 'nullable|date_format:"Y-m-d H:i:s"',
            'typeSave' => 'required|in:1,2'
        ];
    }
}
