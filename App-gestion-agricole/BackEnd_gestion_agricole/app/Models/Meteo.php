<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meteo extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcelle_id',
        'temperature',
        'humidite',
        'pluie'
    ];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
}
