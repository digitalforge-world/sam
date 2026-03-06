<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Endpoints for Locations (Subdivisions)
    Route::get('/regions', [LocationController::class, 'regions']);
    Route::get('/prefectures', [LocationController::class, 'prefectures']);
    Route::get('/cantons', [LocationController::class, 'cantons']);
    Route::get('/villages', [LocationController::class, 'villages']);
    Route::get('/zones', [LocationController::class, 'zones']);

    // Endpoints for Resources
    Route::apiResource('producteurs', \App\Http\Controllers\Api\ProducteurApiController::class);
    Route::apiResource('parcelles', \App\Http\Controllers\Api\ParcelleApiController::class);
    Route::apiResource('organisations', \App\Http\Controllers\Api\OrganisationPaysanneApiController::class);
    Route::get('/cultures', [\App\Http\Controllers\Api\CultureApiController::class, 'index']);
    
    Route::apiResource('identifications', \App\Http\Controllers\Api\IdentificationApiController::class);
    Route::apiResource('controles', \App\Http\Controllers\Api\ControleApiController::class);
    Route::apiResource('arbres', \App\Http\Controllers\Api\ArbreApiController::class);
    Route::get('/parametres', [\App\Http\Controllers\Api\ParametreApiController::class, 'index']);
});
