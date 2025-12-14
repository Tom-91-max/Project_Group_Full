<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Nội dung câu trả lời là bắt buộc.',
            'image.image' => 'File phải là hình ảnh.',
            'image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
        ];
    }
}


// 4. REQUESTS - StoreAnswerRequest
// Path: app/Http/Requests/StoreAnswerRequest.php
// ============================================================================
