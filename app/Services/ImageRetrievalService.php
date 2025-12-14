<?php

namespace App\Services;

use App\Models\PlantImage;

class ImageRetrievalService
{
    public function extractFeatures(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            throw new \InvalidArgumentException('Image file not found: ' . $imagePath);
        }

        // Load image
        $data = @file_get_contents($imagePath);
        if ($data === false) {
            throw new \RuntimeException('Unable to read image file');
        }

        $img = @imagecreatefromstring($data);
        if (! $img) {
            throw new \RuntimeException('Unsupported image format');
        }

        // Resize small for speed
        $w = imagesx($img);
        $h = imagesy($img);
        $thumbW = 64;
        $thumbH = (int) max(1, ($h * $thumbW) / $w);
        $thumb = imagecreatetruecolor($thumbW, $thumbH);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbW, $thumbH, $w, $h);

        // histogram bins per channel
        $bins = 16;
        $hist = array_fill(0, $bins * 3, 0);

        for ($x = 0; $x < $thumbW; $x++) {
            for ($y = 0; $y < $thumbH; $y++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $ri = (int) floor(($r / 256) * $bins);
                $gi = (int) floor(($g / 256) * $bins);
                $bi = (int) floor(($b / 256) * $bins);

                $ri = min($bins - 1, max(0, $ri));
                $gi = min($bins - 1, max(0, $gi));
                $bi = min($bins - 1, max(0, $bi));

                $hist[$ri]++;
                $hist[$bins + $gi]++;
                $hist[$bins * 2 + $bi]++;
            }
        }

        // normalize histogram
        $total = array_sum($hist) ?: 1;
        $vec = array_map(function ($v) use ($total) {
            return $v / $total;
        }, $hist);

        // free resources
        imagedestroy($img);
        imagedestroy($thumb);

        return $vec;
    }

    public function searchSimilar(string $imagePath, int $limit = 3): array
    {
        $queryVector = $this->extractFeatures($imagePath);

        $all = PlantImage::with('plant')->get();

        $results = [];
        foreach ($all as $pi) {
            if (empty($pi->feature_vector) || !is_array($pi->feature_vector)) {
                continue;
            }

            $dist = $this->euclideanDistance($queryVector, $pi->feature_vector);

            $results[] = [
                'plant_id'   => $pi->plant_id,
                'name'       => $pi->plant->name ?? null,
                'distance'   => $dist,
                'image_path' => $pi->image_path,
            ];
        }

        usort($results, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return array_slice($results, 0, $limit);
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $n = max(count($a), count($b));
        $sum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $ai = $a[$i] ?? 0.0;
            $bi = $b[$i] ?? 0.0;
            $d = $ai - $bi;
            $sum += $d * $d;
        }
        return sqrt($sum);
    }
}
