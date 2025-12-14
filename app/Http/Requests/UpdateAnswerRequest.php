<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnswerRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'content' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}

// 5. REQUESTS - UpdateAnswerRequest
// Path: app/Http/Requests/UpdateAnswerRequest.php
// ============================================================================