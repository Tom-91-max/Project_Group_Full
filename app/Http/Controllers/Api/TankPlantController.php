<?php

namespace App\Http\Controllers\Api;

use App\Models\Tank;
use App\Models\TankPlant;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\AttachPlantToTankRequest;

class TankPlantController extends BaseApiController
{
    /**
     * POST /api/tanks/{tank}/plants
     * Gắn cây vào một tank
     */
    public function store(AttachPlantToTankRequest $request, Tank $tank)
    {
        // chỉ owner tank + admin được thêm cây
        $this->authorize('update', $tank);

        $data = $request->validated();

        $tankPlant = TankPlant::firstOrCreate(
            [
                'tank_id'  => $tank->id,
                'plant_id' => $data['plant_id'],
            ],
            [
                'planted_at' => $data['planted_at'] ?? null,
                'note'       => $data['note'] ?? null,
            ]
        );

        return $this->success($tankPlant, 'Plant attached to tank.');
    }

    /**
     * DELETE /api/tank-plants/{tankPlant}
     * Tháo cây khỏi tank
     */
    public function destroy(TankPlant $tankPlant)
    {
        // check quyền dựa trên chủ của tank
        $this->authorize('update', $tankPlant->tank);

        $tankPlant->delete();

        return $this->success(null, 'Plant removed from tank.');
    }
}
