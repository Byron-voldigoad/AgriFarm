<?php

namespace App\Http\Controllers;

use App\Models\Recommandation;
use Illuminate\Http\Request;

class RecommandationController extends Controller
{
    public function index()
    {
        return Recommandation::with(['analyse', 'agriculteur'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contenu' => 'required|string',
            'dateGeneration' => 'required|date',
            'analyse_id' => 'required|exists:Analyse,id',
            'agriculteur_id' => 'required|exists:Utilisateur,id'
        ]);

        return Recommandation::create($data);
    }

    public function show(Recommandation $recommandation)
    {
        return $recommandation->load(['analyse', 'agriculteur']);
    }

    public function update(Request $request, Recommandation $recommandation)
    {
        $data = $request->validate([
            'contenu' => 'string',
            'dateGeneration' => 'date',
            'analyse_id' => 'exists:Analyse,id',
            'agriculteur_id' => 'exists:Utilisateur,id'
        ]);

        $recommandation->update($data);
        return $recommandation;
    }

    public function destroy(Recommandation $recommandation)
    {
        $recommandation->delete();
        return response(null, 204);
    }
}