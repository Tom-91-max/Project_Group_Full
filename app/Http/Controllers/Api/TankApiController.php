<?php

namespace App\Http\Controllers\Api;

use App\Models\Tank;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StoreTankRequest;
use App\Http\Requests\UpdateTankRequest;
use Illuminate\Support\Facades\Auth;

class TankApiController extends BaseApiController
{
    /**
     * GET /api/tanks
     * Lấy danh sách tank của user hiện tại
     */
    public function index()
    {
        $tanks = Tank::where('user_id', Auth::id())
            ->withCount('tankPlants')   // số lượng cây trong tank
            ->get();

        return $this->success($tanks);
    }

    /**
     * POST /api/tanks
     * Tạo tank mới
     */
    public function store(StoreTankRequest $request)
    {
        $data = $request->validated();

        // không tin user_id từ client
        $data['user_id'] = Auth::id();

        $tank = Tank::create($data);

        return $this->success($tank, 'Tank created.');
    }

    /**
     * GET /api/tanks/{tank}
     * Xem chi tiết 1 tank
     */
    public function show(Tank $tank)
    {
        // chỉ owner + admin mới xem được (theo Policy)
        $this->authorize('view', $tank);

        $tank->load([
            'tankPlants.plant',   // list cây trong tank
            'waterLogs',          // history nước
        ]);

        return $this->success($tank);
    }

    /**
     * PUT /api/tanks/{tank}
     * Cập nhật 1 tank
     */
    public function update(UpdateTankRequest $request, Tank $tank)
    {
        $this->authorize('update', $tank);

        $tank->update($request->validated());

        return $this->success($tank, 'Tank updated.');
    }

    /**
     * DELETE /api/tanks/{tank}
     * Xoá 1 tank
     */
    public function destroy(Tank $tank)
    {
        $this->authorize('delete', $tank);

        $tank->delete();

        return $this->success(null, 'Tank deleted.');
    }
}
