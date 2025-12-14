<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;
use App\Models\Plant;
use Illuminate\Http\Request;

class PlantAdminController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Plant::query()->with('taxon');

        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($difficulty = $request->query('difficulty')) {
            $query->where('difficulty', $difficulty);
        }

        if ($light = $request->query('light_level')) {
            $query->where('light_level', $light);
        }

        if ($growth = $request->query('growth_rate')) {
            $query->where('growth_rate', $growth);
        }

        if ($placement = $request->query('placement')) {
            $query->where('placement', $placement);
        }

        $plants = $query->orderByDesc('id')->paginate(20);

        return $this->success($plants);
    }

    public function show(Plant $plant)
    {
        $plant->load('taxon')->loadCount('images');
        return $this->success($plant);
    }

    public function store(StorePlantRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['image_path']) && isset($data['image_sample'])) {
            $data['image_path'] = $data['image_sample'];
        }

        $plant = Plant::create($data);

        return $this->success($plant->load('taxon'), 'Plant created.', 201);
    }

    public function update(UpdatePlantRequest $request, Plant $plant)
    {
        $data = $request->validated();

        if (!isset($data['image_path']) && isset($data['image_sample'])) {
            $data['image_path'] = $data['image_sample'];
        }

        $plant->update($data);

        return $this->success($plant->fresh()->load('taxon'), 'Plant updated.');
    }

    public function destroy(Plant $plant)
    {
        $plant->delete();
        return $this->success(null, 'Plant deleted.');
    }
}
