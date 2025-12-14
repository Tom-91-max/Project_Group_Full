<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaterLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'water_logs';

    protected $fillable = [
        'tank_id',
        'logged_at',
        'ph',
        'temperature',
        'no3',
        'other_params',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'ph' => 'decimal:2',
        'temperature' => 'decimal:1',
        'no3' => 'decimal:2',
        'other_params' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
