<?php

namespace App\Http\Controllers;

use App\Models\Finances;
use Illuminate\Http\Request;

class FinancesController extends Controller
{
    public function index()
    {
        return Finances::with('agriculteur')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dépenseTotale' => 'required|numeric',
            'revenu' => 'required|numeric',
            'agriculteur_id' => 'required|exists:Utilisateur,id'
        ]);

        return Finances::create($data);
    }

    public function show(Finances $finance)
    {
        return $finance->load('agriculteur');
    }

    public function update(Request $request, Finances $finance)
    {
        $data = $request->validate([
            'dépenseTotale' => 'numeric',
            'revenu' => 'numeric',
            'agriculteur_id' => 'exists:Utilisateur,id'
        ]);

        $finance->update($data);
        return $finance;
    }

    public function destroy(Finances $finance)
    {
        $finance->delete();
        return response(null, 204);
    }
}