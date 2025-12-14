<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPlantImageRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120', // max 5MB
        ];
    }
}
