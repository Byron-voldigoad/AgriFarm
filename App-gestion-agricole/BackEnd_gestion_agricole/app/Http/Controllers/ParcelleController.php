<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use Illuminate\Http\Request;

class ParcelleController extends Controller
{
    public function index()
    {
        return Parcelle::with('cultures')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'surface' => 'required|numeric|min:0',
            'localisation' => 'nullable|string|max:255',
            'etat' => 'nullable|string|max:255',
            'agriculteur_id' => 'required|exists:utilisateurs,id',
            'culture_ids' => 'nullable|array',
            'culture_ids.*' => 'exists:cultures,id',
        ]);

        $parcelle = Parcelle::create($validated);

        if (!empty($validated['culture_ids'])) {
            $this->attachCulturesWithPlantationDate($parcelle, $validated['culture_ids']);
        }

        return response()->json($parcelle->load('cultures'), 201);
    }

    public function show(Parcelle $parcelle)
    {
        return $parcelle->load('cultures');
    }

    public function update(Request $request, Parcelle $parcelle)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'surface' => 'sometimes|required|numeric|min:0',
            'localisation' => 'nullable|string|max:255',
            'etat' => 'nullable|string|max:255',
            'agriculteur_id' => 'sometimes|required|exists:utilisateurs,id',
            'culture_ids' => 'nullable|array',
            'culture_ids.*' => 'exists:cultures,id',
            'update_plantation_dates' => 'nullable|boolean' // Nouveau champ pour forcer la mise à jour
        ]);

        $parcelle->update($validated);

        if (isset($validated['culture_ids'])) {
            $currentCultureIds = $parcelle->cultures->pluck('id')->toArray();
            $newCultureIds = $validated['culture_ids'];
            
            // Cultures à ajouter
            $culturesToAdd = array_diff($newCultureIds, $currentCultureIds);
            
            // Cultures à supprimer
            $culturesToRemove = array_diff($currentCultureIds, $newCultureIds);
            
            // Cultures existantes (ne pas modifier la date)
            $existingCultures = array_intersect($currentCultureIds, $newCultureIds);
            
            // Supprimer d'abord les cultures à enlever
            if (!empty($culturesToRemove)) {
                $parcelle->cultures()->detach($culturesToRemove);
            }
            
            // Ajouter les nouvelles cultures avec la date actuelle
            if (!empty($culturesToAdd)) {
                $this->attachCulturesWithPlantationDate($parcelle, $culturesToAdd);
            }
            
            // Mettre à jour la date seulement si explicitement demandé
            if ($request->input('update_plantation_dates', false) && !empty($existingCultures)) {
                $parcelle->cultures()->updateExistingPivot($existingCultures, [
                    'date_plantation' => now()->toDateString()
                ]);
            }
        }

        return response()->json($parcelle->load('cultures'));
    }

    protected function attachCulturesWithPlantationDate(Parcelle $parcelle, array $cultureIds)
    {
        $now = now()->toDateString();
        $attachData = array_fill_keys($cultureIds, ['date_plantation' => $now]);
        $parcelle->cultures()->attach($attachData);
    }

    public function destroy(Parcelle $parcelle)
    {
        $parcelle->cultures()->detach();
        $parcelle->delete();
        return response()->json(null, 204);
    }
}