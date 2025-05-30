<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\Utilisateur;
use App\Models\Parcelle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TacheController extends Controller
{
    // Liste toutes les tâches
    public function index()
    {
        $taches = Tache::with(['ouvrier', 'parcelle'])->get();
        return response()->json($taches);
    }

    // Crée une nouvelle tâche
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'dateDebut' => 'required|date',
            'dateFin' => 'nullable|date|after_or_equal:dateDébut',
            'status' => 'nullable|in:en_attente,en_cours,terminée,annulée',
            'ouvrier_id' => 'required|exists:utilisateurs,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tache = Tache::create($request->all());
        return response()->json($tache, 201);
    }

    // Affiche une tâche spécifique
    public function show($id)
    {
        $tache = Tache::with(['ouvrier', 'parcelle'])->find($id);
        if (!$tache) {
            return response()->json(['message' => 'Tâche non trouvée'], 404);
        }
        return response()->json($tache);
    }

    // Met à jour une tâche
    public function update(Request $request, $id)
    {
        $tache = Tache::find($id);
        if (!$tache) {
            return response()->json(['message' => 'Tâche non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string',
            'dateDebut' => 'sometimes|required|date',
            'dateFin' => 'nullable|date|after_or_equal:dateDébut',
            'status' => 'nullable|in:en_attente,en_cours,terminée,annulée',
            'ouvrier_id' => 'sometimes|required|exists:utilisateurs,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tache->update($request->all());
        return response()->json($tache);
    }

    // Supprime une tâche
    public function destroy($id)
    {
        $tache = Tache::find($id);
        if (!$tache) {
            return response()->json(['message' => 'Tâche non trouvée'], 404);
        }

        $tache->delete();
        return response()->json(['message' => 'Tâche supprimée']);
    }

    // Tâches par ouvrier
    public function tachesParOuvrier($ouvrierId)
    {
        $ouvrier = Utilisateur::find($ouvrierId);
        if (!$ouvrier || $ouvrier->type !== 'OuvrierAgricole') {
            return response()->json(['message' => 'Ouvrier non trouvé'], 404);
        }

        $taches = Tache::where('ouvrier_id', $ouvrierId)->get();
        return response()->json($taches);
    }

    // Tâches par parcelle
    
}