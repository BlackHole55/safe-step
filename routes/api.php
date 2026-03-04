<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimulationController;

Route::prefix('v1')->group(function () {

    Route::prefix('simulation')->group(function () {
        Route::post('/start', [SimulationController::class, 'start']);
        Route::post('/answer', [SimulationController::class, 'answer']);
    });
});
