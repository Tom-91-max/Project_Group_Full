<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'taxon_id' => 'nullable|integer|exists:plant_taxa,id',
            'origin' => 'nullable|string|max:100',

            'ph_min' => 'nullable|numeric|min:0|max:14',
            'ph_max' => 'nullable|numeric|min:0|max:14|gte:ph_min',

            'temp_min' => 'nullable|numeric|min:0|max:60',
            'temp_max' => 'nullable|numeric|min:0|max:60|gte:temp_min',

            'light_level' => 'nullable|in:low,medium,high',
            'difficulty' => 'nullable|in:easy,medium,hard',

            'growth_rate' => 'nullable|in:very_slow,slow,moderate,fast',
            'placement' => 'nullable|in:foreground,midground,background',

            'height_min_cm' => 'nullable|numeric|min:0|max:9999',
            'height_max_cm' => 'nullable|numeric|min:0|max:9999|gte:height_min_cm',

            'water_hardness' => 'nullable|in:very_soft,soft,medium,hard,very_hard',

            'co2_min_mg' => 'nullable|numeric|min:0|max:999999',
            'co2_max_mg' => 'nullable|numeric|min:0|max:999999|gte:co2_min_mg',

            'propagation' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'image_sample' => 'nullable|string|max:255',
            'care_guide' => 'nullable|string',

            'extra' => 'nullable|array',
        ];
    }
}
