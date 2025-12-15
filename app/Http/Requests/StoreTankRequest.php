<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreTankRequest extends FormRequest
{
    public function authorize()
    {
        // chỉ cần user đã đăng nhập
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'size'          => 'nullable|string|max:50',
            'volume_liters' => 'nullable|numeric',
            'substrate'     => 'nullable|string|max:255',
            'light'         => 'nullable|string|max:255',
            'co2'           => 'boolean',
            'description'   => 'nullable|string',
        ];
    }
}
