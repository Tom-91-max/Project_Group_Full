<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'taxon_id',
        'origin',
        'ph_min',
        'ph_max',
        'temp_min',
        'temp_max',
        'light_level',
        'difficulty',
        'growth_rate',
        'placement',
        'height_min_cm',
        'height_max_cm',
        'water_hardness',
        'co2_min_mg',
        'co2_max_mg',
        'propagation',
        'image_path',
        'image_sample',
        'care_guide',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['image_sample'];

    public function tankPlants()
    {
        return $this->hasMany(TankPlant::class);
    }

    public function images()
    {
        return $this->hasMany(PlantImage::class);
    }

    public function taxon()
    {
        return $this->belongsTo(PlantTaxon::class, 'taxon_id');
    }

    public function getImageSampleAttribute()
    {
        return $this->image_path;
    }

    public function setImageSampleAttribute($value)
    {
        $this->attributes['image_path'] = $value;
    }
}
