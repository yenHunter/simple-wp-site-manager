<?php

use App\Http\Controllers\ServerController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('servers.index');
});

Route::middleware(['web'])->group(function () {
    Route::resource('servers', ServerController::class);
    Route::resource('servers.sites', SiteController::class);
});