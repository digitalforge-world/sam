<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;

Route::name('api.')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    // Public lookups for Locations (Accessible to Web Dashboard)
    Route::get('/regions', [LocationController::class, 'regions']);
    Route::get('/prefectures', [LocationController::class, 'prefectures']);
    Route::get('/communes', [LocationController::class, 'communes']);
    Route::get('/cantons', [LocationController::class, 'cantons']);
    Route::get('/villages', [LocationController::class, 'villages']);
    Route::get('/zones', [LocationController::class, 'zones']);
    Route::get('/cultures', [\App\Http\Controllers\Api\CultureApiController::class, 'index']);
    Route::get('/parametres', [\App\Http\Controllers\Api\ParametreApiController::class, 'index']);

    Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckActiveApi::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Protected Actions
        Route::post('/villages', [LocationController::class, 'store_village']);

        // Resources
        Route::apiResource('producteurs', \App\Http\Controllers\Api\ProducteurApiController::class);
        Route::apiResource('parcelles', \App\Http\Controllers\Api\ParcelleApiController::class);
        Route::apiResource('organisations', \App\Http\Controllers\Api\OrganisationPaysanneApiController::class);
        
        Route::apiResource('identifications', \App\Http\Controllers\Api\IdentificationApiController::class);
        Route::apiResource('controles', \App\Http\Controllers\Api\ControleApiController::class);
        Route::apiResource('arbres', \App\Http\Controllers\Api\ArbreApiController::class);
    });
});
