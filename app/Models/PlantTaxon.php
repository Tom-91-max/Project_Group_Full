<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantTaxon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plant_taxa';

    protected $fillable = [
        'genus',
        'species',
        'family',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function plants()
    {
        return $this->hasMany(Plant::class, 'taxon_id');
    }
}
