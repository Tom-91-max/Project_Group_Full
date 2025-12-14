<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plant_id',
        'image_path',
        'feature_vector',
    ];

    protected $casts = [
        'feature_vector' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
