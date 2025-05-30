<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    // GET /api/utilisateurs
    public function index()
    {
        return response()->json(Utilisateur::all());
    }

    // POST /api/utilisateurs
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:Utilisateur',
            'motDePasse' => 'required|string|min:8',
            'type' => 'required|in:Agriculteur,OuvrierAgricole,Administrateur'
        ]);

        $validated['motDePasse'] = bcrypt($validated['motDePasse']);
        
        $utilisateur = Utilisateur::create($validated);
        return response()->json($utilisateur, 201);
    }

    // GET /api/utilisateurs/{id}
    public function show($id)
    {
        return response()->json(Utilisateur::findOrFail($id));
    }

    // PUT /api/utilisateurs/{id}
    public function update(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:Utilisateur,email,'.$utilisateur->id,
            'motDePasse' => 'sometimes|string|min:8',
            'type' => 'sometimes|in:Agriculteur,OuvrierAgricole,Administrateur'
        ]);

        if (isset($validated['motDePasse'])) {
            $validated['motDePasse'] = bcrypt($validated['motDePasse']);
        }

        $utilisateur->update($validated);
        return response()->json($utilisateur);
    }

    // DELETE /api/utilisateurs/{id}
    public function destroy($id)
    {
        Utilisateur::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}