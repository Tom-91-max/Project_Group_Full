<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTankRequest extends FormRequest
{
    public function authorize()
    {
        // quyền cụ thể sẽ check bằng Policy trong controller
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name'          => 'sometimes|string|max:255',
            'size'          => 'sometimes|nullable|string|max:50',
            'volume_liters' => 'sometimes|nullable|numeric',
            'substrate'     => 'sometimes|nullable|string|max:255',
            'light'         => 'sometimes|nullable|string|max:255',
            'co2'           => 'sometimes|boolean',
            'description'   => 'sometimes|nullable|string',
        ];
    }
}
