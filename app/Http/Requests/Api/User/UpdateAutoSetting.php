<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutoSetting extends FormRequest
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
            'content_comment' => 'required_if:auto_comment,1|max:190',
            'content_inbox' => 'required_if:auto_inbox,1|max:190',
            'auto_comment' => 'boolean|required',
            'has_time_inbox' => 'boolean|required',
            'has_time_comment' => 'boolean|required',
            'time_start_comment' => 'nullable|required_if:has_time_comment,1|date_format:H:i',
            'time_end_comment' => 'nullable|required_if:has_time_comment,1|date_format:H:i|after:time_start_comment',
            'time_start_inbox' => 'nullable|required_if:has_time_inbox,1|date_format:H:i',
            'time_end_inbox' => 'nullable|required_if:has_time_inbox,1|date_format:H:i|after:time_start_inbox',
        ];
    }

    public function messages()
    {
        return [
            'content_comment.required_if' => 'Vui lòng nhập nội dung bình luận',
            'content_inbox.required_if' => 'Vui lòng nhập nội dung tin nhắn',
            'time_start_comment.required_if' => 'Vui lòng nhập thời gian bắt đầu chạy auto comment',
            'time_end_comment.required_if' => 'Vui lòng nhập thời gian kết thúc chạy auto comment',
            'time_end_comment.after' => 'Vui lòng nhập thời gian kết thúc lớn hơn thời gian bắt đầu',
            'time_start_inbox.required_if' => 'Vui lòng nhập thời gian bắt đầu chạy auto inbox',
            'time_end_inbox.required_if' => 'Vui lòng nhập thời gian kết thúc chạy auto inbox',
            'time_end_inbox.after' => 'Vui lòng nhập thời gian kết thúc lớn hơn thời gian bắt đầu',
        ];
    }
}
