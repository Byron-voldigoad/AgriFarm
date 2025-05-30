<?php

namespace App\Http\Controllers;

use App\Models\CapteurIoT;
use Illuminate\Http\Request;

class CapteurIoTController extends Controller
{
    public function index()
    {
        return CapteurIoT::with('parcelle')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'valeur' => 'nullable|string',
            'dateMesure' => 'required|date',
            'parcelle_id' => 'required|exists:Parcelle,id'
        ]);

        return CapteurIoT::create($data);
    }

    public function show(CapteurIoT $capteurIoT)
    {
        return $capteurIoT->load('parcelle');
    }

    public function update(Request $request, CapteurIoT $capteurIoT)
    {
        $data = $request->validate([
            'type' => 'string',
            'valeur' => 'nullable|string',
            'dateMesure' => 'date',
            'parcelle_id' => 'exists:Parcelle,id'
        ]);

        $capteurIoT->update($data);
        return $capteurIoT;
    }

    public function destroy(CapteurIoT $capteurIoT)
    {
        $capteurIoT->delete();
        return response(null, 204);
    }
}