<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use Illuminate\Http\Request;

class CultureController extends Controller
{
    public function index()
    {
        return Culture::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|min:2',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'type_culture' => 'required|string',
            'conditions_climatiques' => 'nullable|string',
            'cout_estime' => 'nullable|numeric',
        ]);

        return Culture::create($validated);
    }

    public function show(Culture $culture)
    {
        return $culture;
    }

    public function update(Request $request, Culture $culture)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|min:2',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'type_culture' => 'required|string',
            'conditions_climatiques' => 'nullable|string',
            'cout_estime' => 'nullable|numeric',
        ]);

        $culture->update($validated);
        return $culture;
    }

    public function destroy(Culture $culture)
    {
        $culture->delete();
        return response()->noContent();
    }
}