<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'size',
        'volume_liters',
        'substrate',
        'light',
        'co2',
        'description',
    ];

    protected $casts = [
        'volume_liters' => 'decimal:2',
        'co2' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tankPlants()
    {
        return $this->hasMany(TankPlant::class);
    }

    public function waterLogs()
    {
        return $this->hasMany(WaterLog::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
