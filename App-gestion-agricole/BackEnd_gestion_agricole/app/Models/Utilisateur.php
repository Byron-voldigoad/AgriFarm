<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;
    protected $table = 'utilisateurs'; // Nom de la table si différent du modèle
    protected $fillable = ['nom', 'email', 'password'];

    // Relation inverse avec le modèle Culture
    public function cultures()
    {
        return $this->hasMany(Culture::class, 'agriculteur_id');
    }
}
