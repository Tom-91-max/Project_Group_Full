<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TankPlant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tank_plants';

    protected $fillable = [
        'tank_id',
        'plant_id',
        'planted_at',
        'note',
    ];

    protected $casts = [
        'planted_at' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function plantLogs()
    {
        return $this->hasMany(PlantLog::class);
    }
}
