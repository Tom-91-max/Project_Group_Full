<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tank_id' => 'nullable|exists:tanks,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề câu hỏi là bắt buộc.',
            'content.required' => 'Nội dung câu hỏi là bắt buộc.',
            'tank_id.exists' => 'Bể được chọn không tồn tại.',
            'image.image' => 'File phải là hình ảnh.',
            'image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
        ];
    }
}




// 2. REQUESTS - StoreQuestionRequest
// Path: app/Http/Requests/StoreQuestionRequest.php
// ============================================================================