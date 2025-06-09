<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Role;

Route::get('/roles', function () {
    return response()->json(Role::all());
});
