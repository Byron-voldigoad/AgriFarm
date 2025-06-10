<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Culture extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'photo',
        'type_culture',
        'conditions_climatiques',
        'cout_estime',
    ];

}