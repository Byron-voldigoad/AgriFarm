<?php

use Illuminate\Support\Facades\Route;

Route::apiResources([
    'utilisateurs' => 'App\Http\Controllers\UtilisateurController',
    'cultures' => 'App\Http\Controllers\CultureController',
    'parcelles' => 'App\Http\Controllers\ParcelleController',
    'finances' => 'App\Http\Controllers\FinancesController',
    'rendements' => 'App\Http\Controllers\RendementController',
    'taches' => 'App\Http\Controllers\TacheController',
    'notifications' => 'App\Http\Controllers\NotificationController',
    'capteurs' => 'App\Http\Controllers\CapteurIoTController',
    'analyses' => 'App\Http\Controllers\AnalyseController',
    'recommandations' => 'App\Http\Controllers\RecommandationController',
    'dashboard' => 'App\Http\Controllers\DashboardController'
]);

require __DIR__.'/api_roles.php';


// Route::get('/dashboard', [DashboardController::class, 'index']);