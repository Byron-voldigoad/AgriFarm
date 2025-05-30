<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendement extends Model
{
    use HasFactory;

    // Ajoutez cette méthode pour la relation
    public function culture()
    {
        return $this->belongsTo(\App\Models\Culture::class, 'culture_id');
    }
}
