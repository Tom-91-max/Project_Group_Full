<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantLogRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'logged_at' => 'required|date',
            'height'    => 'nullable|numeric',
            'status'    => 'nullable|string|max:255',
            'note'      => 'nullable|string',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
