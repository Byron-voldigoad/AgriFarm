<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcelle extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'surface',
        'localisation',
        'etat',
        'agriculteur_id',
        'culture_id',
        'stadeCroissance',
        'date_plantation' => 'date',
    ];

    public function agriculteur()
{
    return $this->belongsTo(Utilisateur::class, 'agriculteur_id');
}
    public function cultures()
    {
        return $this->belongsToMany(Culture::class, 'parcelle_culture');
    }

    public function capteurs()
    {
        return $this->hasMany(CapteurIoT::class);
    }

    public function getPlantationDateAttribute()
{
    return $this->pivot->date_plantation ?? null;
}

}