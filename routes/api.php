<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicalFacilityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;

// Auth Routes
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/profile', [AuthController::class, 'profile']);

    // Medical Facilities Protected Routes
    Route::put('medical-facilities/{facility}', [MedicalFacilityController::class, 'update']);
    Route::delete('medical-facilities/{facility}', [MedicalFacilityController::class, 'destroy']);
    Route::apiResource('units', UnitController::class);
});

// Public Medical Facility Routes
Route::get('medical-facilities/search', [MedicalFacilityController::class, 'search']);
Route::get('medical-facilities', [MedicalFacilityController::class, 'index']);
Route::get('medical-facilities/{facility}', [MedicalFacilityController::class, 'show']);
