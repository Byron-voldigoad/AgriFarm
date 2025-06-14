<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parcelle;

class Recommandation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcelle_id',
        'titre',
        'contenu',
        'type',
        'priorite',
        'status',
    ];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
}
