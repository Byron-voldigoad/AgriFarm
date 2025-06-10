<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UtilisateurController extends Controller
{
    // GET /api/utilisateurs
    public function index()
    {
        $utilisateurs = Utilisateur::with('roles')->get();
        return response()->json($utilisateurs);
    }

    // POST /api/utilisateurs
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs',
            'motDePasse' => 'nullable|string|min:8', // Rendre le champ motDePasse optionnel
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'photo' => 'nullable|string', // Ajout de la validation du champ photo
        ]);

        // Générer un mot de passe par défaut si non fourni
        if (empty($validated['motDePasse'])) {
            $validated['motDePasse'] = bcrypt(Str::random(10)); // Remplacement de str_random() par Str::random()
        } else {
            $validated['motDePasse'] = bcrypt($validated['motDePasse']);
        }

        $utilisateur = Utilisateur::create($validated);
        $utilisateur->roles()->sync($request->roles);
        return response()->json($utilisateur->load('roles'), 201);
    }

    // GET /api/utilisateurs/{id}
    public function show($id)
    {
        return response()->json(Utilisateur::with('roles')->findOrFail($id));
    }

    // PUT /api/utilisateurs/{id}
    public function update(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:utilisateurs,email,'.$utilisateur->id,
            'motDePasse' => 'sometimes|string|min:8',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'actif' => 'sometimes|boolean',
            'photo' => 'nullable|string', // Ajout de la validation du champ photo
        ]);
        if (isset($validated['motDePasse'])) {
            $validated['motDePasse'] = bcrypt($validated['motDePasse']);
        }
        $utilisateur->update($validated);
        if ($request->has('roles')) {
            $utilisateur->roles()->sync($request->roles);
        }
        return response()->json($utilisateur->load('roles'));
    }

    // DELETE /api/utilisateurs/{id}
    public function destroy($id)
    {
        Utilisateur::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}