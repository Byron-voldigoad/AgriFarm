<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\CultureController;
use App\Http\Controllers\ParcelleController;
use App\Http\Controllers\FinancesController;
use App\Http\Controllers\RendementController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CapteurIoTController;
use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\RecommandationController;
use App\Http\Controllers\DashboardController;

// Routes spécifiques pour les utilisateurs
Route::get('/utilisateurs/agriculteurs', [UtilisateurController::class, 'getAgriculteurs']);

// Routes CRUD génériques
Route::apiResources([
    'utilisateurs' => UtilisateurController::class,
    'cultures' => CultureController::class,
    'parcelles' => ParcelleController::class,
    'finances' => FinancesController::class,
    'rendements' => RendementController::class,
    'taches' => TacheController::class,
    'notifications' => NotificationController::class,
    'capteurs' => CapteurIoTController::class,
    'analyses' => AnalyseController::class,
    'recommandations' => RecommandationController::class,
    'dashboard' => DashboardController::class
]);

require __DIR__.'/api_roles.php';

Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show']);

// Route::get('/dashboard', [DashboardController::class, 'index']);