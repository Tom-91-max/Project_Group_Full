<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plant_logs';

    protected $fillable = [
        'tank_plant_id',
        'logged_at',
        'height',
        'status',
        'note',
        'image_path',
    ];

    protected $casts = [
        'logged_at' => 'date',
        'height' => 'decimal:1',
        'deleted_at' => 'datetime',
    ];

    public function tankPlant()
    {
        return $this->belongsTo(TankPlant::class);
    }
}
