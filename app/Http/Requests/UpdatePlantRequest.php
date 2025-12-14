<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',

            'taxon_id' => 'sometimes|nullable|integer|exists:plant_taxa,id',
            'origin' => 'sometimes|nullable|string|max:100',

            'ph_min' => 'sometimes|nullable|numeric|min:0|max:14',
            'ph_max' => 'sometimes|nullable|numeric|min:0|max:14|gte:ph_min',

            'temp_min' => 'sometimes|nullable|numeric|min:0|max:60',
            'temp_max' => 'sometimes|nullable|numeric|min:0|max:60|gte:temp_min',

            'light_level' => 'sometimes|nullable|in:low,medium,high',
            'difficulty' => 'sometimes|nullable|in:easy,medium,hard',

            'growth_rate' => 'sometimes|nullable|in:very_slow,slow,moderate,fast',
            'placement' => 'sometimes|nullable|in:foreground,midground,background',

            'height_min_cm' => 'sometimes|nullable|numeric|min:0|max:9999',
            'height_max_cm' => 'sometimes|nullable|numeric|min:0|max:9999|gte:height_min_cm',

            'water_hardness' => 'sometimes|nullable|in:very_soft,soft,medium,hard,very_hard',

            'co2_min_mg' => 'sometimes|nullable|numeric|min:0|max:999999',
            'co2_max_mg' => 'sometimes|nullable|numeric|min:0|max:999999|gte:co2_min_mg',

            'propagation' => 'sometimes|nullable|string',
            'image_path' => 'sometimes|nullable|string|max:255',
            'image_sample' => 'sometimes|nullable|string|max:255',
            'care_guide' => 'sometimes|nullable|string',

            'extra' => 'sometimes|nullable|array',
        ];
    }
}
