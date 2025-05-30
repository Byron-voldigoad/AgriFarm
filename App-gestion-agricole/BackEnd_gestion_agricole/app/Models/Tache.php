<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'dateDebut',
        'dateFin',
        'status',
        'ouvrier_id'
    ];

    protected $dates = ['dateDebut', 'dateFin'];

    // Relation avec l'ouvrier
    public function ouvrier()
    {
        return $this->belongsTo(Utilisateur::class, 'ouvrier_id');
    }

    // Relation avec la parcelle (si vous voulez associer des tâches à des parcelles)
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }

    // Statuts possibles
    public static function statuts()
    {
        return [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'terminée' => 'Terminée',
            'annulée' => 'Annulée'
        ];
    }
}