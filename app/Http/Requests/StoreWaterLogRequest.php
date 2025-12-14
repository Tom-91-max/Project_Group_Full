<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization sẽ được xử lý trong Controller bằng Policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'logged_at'   => 'required|date|before_or_equal:now',
            'ph'          => 'nullable|numeric|min:0|max:14',
            'temperature' => 'nullable|numeric|min:0|max:50', // Celsius
            'no3'         => 'nullable|numeric|min:0', // Nitrate (mg/L)
            'gh'          => 'nullable|numeric|min:0', // General Hardness (dGH)
            'kh'          => 'nullable|numeric|min:0', // Carbonate Hardness (dKH)
            'note'        => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'logged_at.required' => 'Thời gian đo là bắt buộc.',
            'logged_at.before_or_equal' => 'Không thể đo trong tương lai.',
            'ph.min' => 'Giá trị pH phải từ 0-14.',
            'ph.max' => 'Giá trị pH phải từ 0-14.',
            'temperature.min' => 'Nhiệt độ không hợp lệ.',
            'temperature.max' => 'Nhiệt độ không hợp lệ.',
        ];
    }

    /**
     * Custom attribute names for error messages
     */
    public function attributes(): array
    {
        return [
            'logged_at' => 'thời gian đo',
            'ph' => 'độ pH',
            'temperature' => 'nhiệt độ',
            'no3' => 'nồng độ NO3',
            'gh' => 'độ cứng tổng (GH)',
            'kh' => 'độ cứng cacbonat (KH)',
            'note' => 'ghi chú',
        ];
    }
}