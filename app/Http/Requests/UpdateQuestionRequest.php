<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'tank_id' => 'nullable|exists:tanks,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}


// 3. REQUESTS - UpdateQuestionRequest
// Path: app/Http/Requests/UpdateQuestionRequest.php
// ============================================================================