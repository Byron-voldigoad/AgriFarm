<?php

namespace App\Http\Controllers;

use App\Models\Rendement;
use Illuminate\Http\Request;

class RendementController extends Controller
{
    public function index()
    {
        return Rendement::with('culture')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quantité' => 'required|numeric',
            'couts' => 'required|numeric',
            'date' => 'required|date',
            'culture_id' => 'required|exists:Culture,id'
        ]);

        return Rendement::create($data);
    }

    public function show(Rendement $rendement)
    {
        return $rendement->load('culture');
    }

    public function update(Request $request, Rendement $rendement)
    {
        $data = $request->validate([
            'quantité' => 'numeric',
            'couts' => 'numeric',
            'date' => 'date',
            'culture_id' => 'exists:Culture,id'
        ]);

        $rendement->update($data);
        return $rendement;
    }

    public function destroy(Rendement $rendement)
    {
        $rendement->delete();
        return response(null, 204);
    }
}