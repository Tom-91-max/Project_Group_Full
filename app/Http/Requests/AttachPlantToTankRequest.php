<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class AttachPlantToTankRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'plant_id'   => 'required|exists:plants,id',
            'planted_at' => 'nullable|date',
            'note'       => 'nullable|string',
        ];
    }
}
