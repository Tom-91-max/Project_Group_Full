<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreWaterLogRequest;
use App\Models\Tank;
use App\Models\WaterLog;
use App\Services\AdvisorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WaterLogApiController extends BaseApiController
{
    protected AdvisorService $advisor;

    public function __construct(AdvisorService $advisor)
    {
        $this->advisor = $advisor;
    }

    /**
     * Lấy danh sách water logs của một tank
     * GET /api/tanks/{tank}/water-logs
     */
    public function index(Tank $tank, Request $request): JsonResponse
    {
        // Chỉ owner hoặc admin mới xem được logs
        $this->authorize('view', $tank);

        // Lấy tối đa 50 logs gần nhất, có thể tùy chỉnh qua query param
        $limit = $request->input('limit', 50);
        $limit = min($limit, 100); // Max 100 records

        $logs = $tank->waterLogs()
                     ->orderByDesc('logged_at')
                     ->limit($limit)
                     ->get();

        return $this->success([
            'logs' => $logs,
            'count' => $logs->count(),
            'tank_name' => $tank->name,
        ], 'Water logs retrieved successfully.');
    }

    /**
     * Lưu water log mới + nhận gợi ý từ Advisor
     * POST /api/tanks/{tank}/water-logs
     */
    public function store(StoreWaterLogRequest $request, Tank $tank): JsonResponse
    {
        // Chỉ owner hoặc admin mới thêm logs
        $this->authorize('update', $tank);

        $data = $request->validated();

        // Tạo water log mới
        $waterLog = $tank->waterLogs()->create([
            'logged_at'    => $data['logged_at'],
            'ph'           => $data['ph'] ?? null,
            'temperature'  => $data['temperature'] ?? null,
            'no3'          => $data['no3'] ?? null,
            'other_params' => json_encode([
                'gh'   => $data['gh'] ?? null,
                'kh'   => $data['kh'] ?? null,
                'note' => $data['note'] ?? null,
            ]),
        ]);

        // Gọi AdvisorService để phân tích và đưa ra gợi ý
        $advisorResult = $this->advisor->evaluate($tank, $waterLog);

        return $this->success([
            'log'     => $waterLog,
            'advisor' => $advisorResult,
        ], 'Water log saved and analyzed successfully.');
    }

    /**
     * Xem chi tiết một water log
     * GET /api/water-logs/{waterLog}
     */
    public function show(WaterLog $waterLog): JsonResponse
    {
        // Authorize: chỉ owner của tank chứa log này
        $this->authorize('view', $waterLog->tank);

        // Decode other_params để hiển thị dễ đọc
        $waterLog->decoded_params = json_decode($waterLog->other_params, true);

        return $this->success($waterLog, 'Water log details.');
    }

    /**
     * Xóa một water log
     * DELETE /api/water-logs/{waterLog}
     */
    public function destroy(WaterLog $waterLog): JsonResponse
    {
        // Authorize: chỉ owner hoặc admin
        $this->authorize('delete', $waterLog->tank);

        $waterLog->delete();

        return $this->success(null, 'Water log deleted successfully.');
    }
}