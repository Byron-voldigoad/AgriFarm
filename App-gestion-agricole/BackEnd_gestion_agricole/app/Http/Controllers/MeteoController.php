<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeteoController extends Controller
{
    public function store(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'parcelle_id' => 'required|integer|exists:parcelles,id',
            'temperature' => 'required|numeric',
            'humidite' => 'required|numeric',
            'pluie' => 'required|numeric'
        ]);

        // Sauvegarder les données dans la base de données
        $meteo = new \App\Models\Meteo();
        $meteo->parcelle_id = $validated['parcelle_id'];
        $meteo->temperature = $validated['temperature'];
        $meteo->humidite = $validated['humidite'];
        $meteo->pluie = $validated['pluie'];
        $meteo->save();

        return response()->json(['message' => 'Données météo enregistrées avec succès']);
    }
}
