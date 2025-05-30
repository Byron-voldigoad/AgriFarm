<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function index()
    {
        return Analyse::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dateAnalyse' => 'required|date',
            'description' => 'nullable|string',
            'ia_id' => 'nullable|integer'
        ]);

        return Analyse::create($data);
    }

    public function show(Analyse $analyse)
    {
        return $analyse;
    }

    public function update(Request $request, Analyse $analyse)
    {
        $data = $request->validate([
            'dateAnalyse' => 'date',
            'description' => 'nullable|string',
            'ia_id' => 'nullable|integer'
        ]);

        $analyse->update($data);
        return $analyse;
    }

    public function destroy(Analyse $analyse)
    {
        $analyse->delete();
        return response(null, 204);
    }
}