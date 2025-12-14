<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StorePlantLogRequest;
use App\Http\Requests\UpdatePlantLogRequest;
use App\Models\PlantLog;
use App\Models\TankPlant;
use Illuminate\Support\Facades\Storage;

class PlantLogController extends BaseApiController
{
    public function index(TankPlant $tankPlant)
    {
        $this->authorize('view', $tankPlant->tank);

        $logs = $tankPlant->plantLogs()->orderByDesc('logged_at')->get();

        return $this->success($logs);
    }

    public function store(StorePlantLogRequest $request, TankPlant $tankPlant)
    {
        $this->authorize('update', $tankPlant->tank);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('plant_logs', 'public');
        }

        $log = $tankPlant->plantLogs()->create([
            'logged_at' => $data['logged_at'],
            'height'    => $data['height'] ?? null,
            'status'    => $data['status'] ?? null,
            'note'      => $data['note'] ?? null,
            'image_path'=> $data['image_path'] ?? null,
        ]);

        return $this->success($log, 'Plant log created.');
    }

    public function update(UpdatePlantLogRequest $request, PlantLog $plantLog)
    {
        $this->authorize('update', $plantLog);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            try { if ($plantLog->image_path) Storage::disk('public')->delete($plantLog->image_path); } catch (\Throwable $_) {}
            $data['image_path'] = $request->file('image')->store('plant_logs', 'public');
        }

        $plantLog->update(array_filter($data, function ($v) { return $v !== null; }));

        return $this->success($plantLog, 'Plant log updated.');
    }

    public function destroy(PlantLog $plantLog)
    {
        $this->authorize('delete', $plantLog);

        try { if ($plantLog->image_path) Storage::disk('public')->delete($plantLog->image_path); } catch (\Throwable $_) {}
        $plantLog->delete();

        return $this->success(null, 'Plant log deleted.');
    }
}
