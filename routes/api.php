<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController; 
use App\Http\Controllers\MedicalFacilitiesController; 
use App\Http\Controllers\MedicalFacilitiesUnitController; 
use App\Http\Controllers\SearchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/login', [ApiController::class, 'login']);
Route::post('auth/register', [ApiController::class, 'register']);


Route::group(['middleware' => 'auth:sanctum'], function(){

    Route::post('auth/logout', [ApiController::class, 'logout']);

    Route::get('auth/user', [ApiController::class, 'user']);

    Route::post('medical-facilities', [MedicalFacilitiesController::class, 'store']);

    Route::post('medical-facilities/{facility_id}/units', [MedicalFacilitiesUnitController::class, 'store']);

    Route::put('medical-facilities/{id}', [MedicalFacilitiesController::class, 'update']);

    Route::put('medical-facilities/{facility_id}/units/{id}', [MedicalFacilitiesUnitController::class, 'update']);

    Route::delete('medical-facilities/{id}', [MedicalFacilitiesController::class, 'destroy']);

    Route::delete('medical-facilities/{facility_id}/units/{id}', [MedicalFacilitiesUnitController::class, 'destroy']);



});

Route::get('medical-facilities/search', [MedicalFacilitiesController::class, 'search']);

Route::get('medical-facilities/filter', [MedicalFacilitiesController::class, 'filter']);

Route::get('medical-facilities/{id}', [MedicalFacilitiesController::class, 'show']);

Route::get('medical-facilities/{facility_id}/units/{id}', [MedicalFacilitiesUnitController::class, 'show']);

