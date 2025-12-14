<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\UploadPlantImageRequest;
use App\Models\Plant;
use App\Models\PlantImage;
use App\Services\ImageRetrievalService;
use Illuminate\Support\Facades\Storage;

class PlantImageAdminController extends BaseApiController
{
    public function store(UploadPlantImageRequest $request, Plant $plant, ImageRetrievalService $service)
    {
        $file = $request->file('image');

        // store file in public disk under plant_images
        $path = $file->store('plant_images', 'public');
        $fullPath = Storage::disk('public')->path($path);

        try {
            $features = $service->extractFeatures($fullPath);

            $pi = PlantImage::create([
                'plant_id'       => $plant->id,
                'image_path'     => $path,
                'feature_vector' => $features,
            ]);

            return $this->success($pi, 'Plant image saved.');
        } catch (\Throwable $e) {
            // On failure remove uploaded file
            try { Storage::disk('public')->delete($path); } catch (\Throwable $_) {}
            return $this->fromException($e);
        }
    }

    public function destroy(PlantImage $plantImage)
    {
        // route is protected by admin middleware; still safe to check
        try {
            Storage::disk('public')->delete($plantImage->image_path);
        } catch (\Throwable $_) {}

        $plantImage->delete();

        return $this->success(null, 'Plant image deleted.');
    }
}
