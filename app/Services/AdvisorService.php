<?php

namespace App\Services;

use App\Models\Tank;
use App\Models\WaterLog;

class AdvisorService
{
    /**
     * Phân tích thông số nước và đưa ra gợi ý
     * 
     * @param Tank $tank
     * @param WaterLog $log
     * @return array ['score' => int, 'messages' => array, 'level' => string]
     */
    public function evaluate(Tank $tank, WaterLog $log): array
    {
        // Lấy tất cả cây trong bể
        $plants = $tank->tankPlants()->with('plant')->get()->pluck('plant');

        $messages = [];
        $warnings = [];
        $tips = [];
        $score = 100; // Điểm tối đa

        // Nếu không có cây, chỉ đưa ra gợi ý chung
        if ($plants->isEmpty()) {
            return $this->evaluateEmptyTank($log);
        }

        // 1. ĐÁNH GIÁ pH
        if ($log->ph !== null) {
            foreach ($plants as $plant) {
                if ($plant->ph_min && $plant->ph_max) {
                    if ($log->ph < $plant->ph_min) {
                        $score -= 5;
                        $warnings[] = "🔴 pH ({$log->ph}) thấp hơn mức tối thiểu cho {$plant->name} (tối thiểu: {$plant->ph_min}).";
                        $tips[] = "💡 Thêm baking soda hoặc dùng đá vôi để tăng pH.";
                    } elseif ($log->ph > $plant->ph_max) {
                        $score -= 5;
                        $warnings[] = "🔴 pH ({$log->ph}) cao hơn mức tối đa cho {$plant->name} (tối đa: {$plant->ph_max}).";
                        $tips[] = "💡 Thêm lá nhựa ruồi hoặc driftwood để giảm pH tự nhiên.";
                    } else {
                        $messages[] = "✅ pH phù hợp với {$plant->name}.";
                    }
                }
            }
        }

        // 2. ĐÁNH GIÁ NHIỆT ĐỘ
        if ($log->temperature !== null) {
            foreach ($plants as $plant) {
                if ($plant->temperature_min && $plant->temperature_max) {
                    if ($log->temperature < $plant->temperature_min) {
                        $score -= 8;
                        $warnings[] = "🔴 Nhiệt độ ({$log->temperature}°C) quá thấp cho {$plant->name} (tối thiểu: {$plant->temperature_min}°C).";
                        $tips[] = "💡 Sử dụng máy sưởi bể cá để tăng nhiệt độ.";
                    } elseif ($log->temperature > $plant->temperature_max) {
                        $score -= 8;
                        $warnings[] = "🔴 Nhiệt độ ({$log->temperature}°C) quá cao cho {$plant->name} (tối đa: {$plant->temperature_max}°C).";
                        $tips[] = "💡 Tăng cường thông gió hoặc dùng quạt làm mát bề mặt nước.";
                    } else {
                        $messages[] = "✅ Nhiệt độ phù hợp với {$plant->name}.";
                    }
                }
            }
        }

        // 3. ĐÁNH GIÁ NO3 (Nitrate)
        if ($log->no3 !== null) {
            if ($log->no3 > 40) {
                $score -= 10;
                $warnings[] = "🔴 NO3 ({$log->no3} mg/L) quá cao! Nguy cơ tảo bùng phát.";
                $tips[] = "💡 Thay nước 30-50%, tăng cây thủy sinh để hấp thụ NO3.";
            } elseif ($log->no3 > 20) {
                $score -= 5;
                $warnings[] = "🟡 NO3 ({$log->no3} mg/L) hơi cao. Theo dõi thường xuyên.";
                $tips[] = "💡 Xem xét thay nước định kỳ hàng tuần.";
            } else {
                $messages[] = "✅ NO3 ở mức tốt ({$log->no3} mg/L).";
            }
        }

        // 4. ĐÁNH GIÁ GH & KH (từ other_params)
        $otherParams = json_decode($log->other_params, true);
        
        if (isset($otherParams['gh']) && $otherParams['gh'] !== null) {
            $gh = $otherParams['gh'];
            if ($gh < 4) {
                $warnings[] = "🟡 GH ({$gh} dGH) quá mềm. Cây có thể thiếu khoáng chất.";
                $tips[] = "💡 Thêm phân bón vi lượng hoặc Seachem Equilibrium.";
            } elseif ($gh > 15) {
                $warnings[] = "🟡 GH ({$gh} dGH) quá cứng. Không phù hợp với cây mềm nước.";
            } else {
                $messages[] = "✅ GH ở mức tốt ({$gh} dGH).";
            }
        }

        if (isset($otherParams['kh']) && $otherParams['kh'] !== null) {
            $kh = $otherParams['kh'];
            if ($kh < 2) {
                $score -= 3;
                $warnings[] = "🟡 KH ({$kh} dKH) thấp. pH có thể dao động mạnh.";
                $tips[] = "💡 Thêm baking soda hoặc crushed coral để ổn định pH.";
            } elseif ($kh > 10) {
                $warnings[] = "🟡 KH ({$kh} dKH) cao. Khó điều chỉnh pH.";
            } else {
                $messages[] = "✅ KH ổn định ({$kh} dKH).";
            }
        }

        // 5. XU HƯỚNG (nếu có nhiều logs)
        $trendAnalysis = $this->analyzeTrend($tank, $log);
        if (!empty($trendAnalysis)) {
            $messages = array_merge($messages, $trendAnalysis);
        }

        // 6. GỢI Ý TỔNG HỢP
        $generalTips = $this->getGeneralTips($log, $plants);
        $tips = array_merge($tips, $generalTips);

        // Đảm bảo score trong khoảng 0-100
        $score = max(0, min(100, $score));

        // Xác định mức độ
        $level = $this->getHealthLevel($score);

        return [
            'score'    => $score,
            'level'    => $level,
            'messages' => $messages,
            'warnings' => $warnings,
            'tips'     => array_unique($tips), // Loại bỏ tips trùng lặp
            'summary'  => $this->getSummary($score, $warnings),
        ];
    }

    /**
     * Đánh giá bể không có cây
     */
    private function evaluateEmptyTank(WaterLog $log): array
    {
        $messages = [];
        $score = 100;

        if ($log->ph !== null) {
            if ($log->ph >= 6.5 && $log->ph <= 7.5) {
                $messages[] = "✅ pH ({$log->ph}) ở mức trung tính, phù hợp cho hầu hết các loại cây.";
            } else {
                $messages[] = "🟡 pH ({$log->ph}) hơi lệch khỏi khoảng trung tính (6.5-7.5).";
            }
        }

        return [
            'score'    => $score,
            'level'    => 'good',
            'messages' => $messages,
            'warnings' => [],
            'tips'     => ['💡 Thêm cây thủy sinh để bể đạt hiệu quả tối ưu!'],
            'summary'  => 'Bể chưa có cây. Thêm cây để nhận gợi ý chi tiết hơn.',
        ];
    }

    /**
     * Phân tích xu hướng (so sánh với log trước đó)
     */
    private function analyzeTrend(Tank $tank, WaterLog $currentLog): array
    {
        $messages = [];

        // Lấy log trước đó
        $previousLog = $tank->waterLogs()
            ->where('logged_at', '<', $currentLog->logged_at)
            ->orderByDesc('logged_at')
            ->first();

        if (!$previousLog) {
            return $messages; // Chưa có log trước
        }

        // So sánh pH
        if ($currentLog->ph && $previousLog->ph) {
            $phChange = $currentLog->ph - $previousLog->ph;
            if (abs($phChange) > 0.5) {
                $direction = $phChange > 0 ? 'tăng' : 'giảm';
                $messages[] = "📈 pH {$direction} đáng kể (" . abs(round($phChange, 2)) . " điểm so với lần đo trước).";
            }
        }

        // So sánh nhiệt độ
        if ($currentLog->temperature && $previousLog->temperature) {
            $tempChange = $currentLog->temperature - $previousLog->temperature;
            if (abs($tempChange) > 2) {
                $direction = $tempChange > 0 ? 'tăng' : 'giảm';
                $messages[] = "🌡️ Nhiệt độ {$direction} " . abs(round($tempChange, 1)) . "°C so với lần đo trước.";
            }
        }

        return $messages;
    }

    /**
     * Gợi ý chung dựa trên tổng hợp thông số
     */
    private function getGeneralTips(WaterLog $log, $plants): array
    {
        $tips = [];

        // Nếu tất cả thông số đều tốt
        $allGood = true;
        if ($log->ph && ($log->ph < 6.0 || $log->ph > 8.0)) $allGood = false;
        if ($log->no3 && $log->no3 > 20) $allGood = false;

        if ($allGood) {
            $tips[] = "🎉 Thông số nước rất tốt! Tiếp tục duy trì chế độ chăm sóc hiện tại.";
        }

        // Gợi ý chung về thay nước
        if ($log->no3 > 30 || !$log->no3) {
            $tips[] = "💧 Nên thay 20-30% nước mỗi tuần để duy trì chất lượng nước.";
        }

        return $tips;
    }

    /**
     * Xác định mức độ sức khỏe
     */
    private function getHealthLevel(int $score): string
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 75) return 'good';
        if ($score >= 50) return 'fair';
        return 'poor';
    }

    /**
     * Tạo summary text
     */
    private function getSummary(int $score, array $warnings): string
    {
        if ($score >= 90) {
            return 'Bể cá của bạn đang ở trạng thái tuyệt vời! 🌟';
        }
        if ($score >= 75) {
            return 'Bể cá đang hoạt động tốt. Theo dõi thường xuyên để duy trì.';
        }
        if ($score >= 50) {
            return 'Có một số vấn đề cần chú ý. Xem các cảnh báo bên dưới.';
        }
        return 'Bể cá đang gặp vấn đề nghiêm trọng! Cần hành động ngay. ⚠️';
    }
}