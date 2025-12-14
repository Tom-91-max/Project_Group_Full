<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\SearchPlantByImageRequest;
use App\Models\PlantImage;
use App\Services\ImageRetrievalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageRetrievalController extends BaseApiController
{
    public function search(SearchPlantByImageRequest $request, ImageRetrievalService $service)
    {
        $data = $request->validated();

        $limit = $data['limit'] ?? 5;

        // store uploaded file temporarily on public disk
        $path = $request->file('image')->store('plant_images/temp', 'public');
        $fullPath = Storage::disk('public')->path($path);

        try {
            $results = $service->searchSimilar($fullPath, $limit);

            // convert distances to similarity score (relative)
            $max = 0.0;
            foreach ($results as $r) {
                if ($r['distance'] > $max) $max = $r['distance'];
            }

            $payload = [];
            foreach ($results as $r) {
                $similarity = 0;
                if ($max > 0) {
                    $similarity = (int)round(100 * (1 - ($r['distance'] / $max)));
                    $similarity = max(0, min(100, $similarity));
                }

                $payload[] = [
                    'plant_id'   => $r['plant_id'],
                    'name'       => $r['name'] ?? null,
                    'similarity' => $similarity,
                    'image_path' => $r['image_path'] ?? null,
                ];
            }

            return $this->success($payload);
        } catch (\Throwable $e) {
            return $this->fromException($e);
        } finally {
            // cleanup temp file
            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $_) {
                // ignore
            }
        }
    }
}
