<?php

namespace App\Http\Controllers;

use App\Models\Recommandation;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class RecommandationController extends Controller
{
    public function index()
    {
        // Récupérer toutes les recommandations existantes
        $recommandations = Recommandation::with('parcelle')->get();
        return $recommandations;
    }

    public function store(Request $request)
    {
        // À implémenter
    }

    public function show(Recommandation $recommandation)
    {
        // À implémenter
    }

    public function update(Request $request, Recommandation $recommandation)
    {
        // À implémenter
    }

    public function destroy(Recommandation $recommandation)
    {
        // À implémenter
    }
}
