<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPlantByImageRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'limit' => 'nullable|integer|min:1|max:20',
        ];
    }
}
