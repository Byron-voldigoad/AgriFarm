<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return Notification::with('ouvrier')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string',
            'dateNotification' => 'required|date',
            'ouvrier_id' => 'required|exists:Utilisateur,id'
        ]);

        return Notification::create($data);
    }

    public function show(Notification $notification)
    {
        return $notification->load('ouvrier');
    }

    public function update(Request $request, Notification $notification)
    {
        $data = $request->validate([
            'message' => 'string',
            'dateNotification' => 'date',
            'ouvrier_id' => 'exists:Utilisateur,id'
        ]);

        $notification->update($data);
        return $notification;
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response(null, 204);
    }
}