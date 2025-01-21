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

Route::post('login', [ApiController::class, 'login']);
Route::post('register', [ApiController::class, 'register']);


Route::group(['middleware' => 'auth:sanctum'], function(){

    Route::post('logout', [ApiController::class, 'logout']);

});
Route::post('medical-facilities', [MedicalFacilitiesController::class, 'store']);

Route::get('medical-facilities/search', [MedicalFacilitiesController::class, 'search']);

Route::get('medical-facilities/filter', [MedicalFacilitiesController::class, 'filter']);


Route::get('medical-facilities/{id}', [MedicalFacilitiesController::class, 'show']);

 //Update a medical facility
Route::put('medical-facilities/{id}', [MedicalFacilitiesController::class, 'update']);


 // Delete a medical facility
Route::delete('medical-facilities/{id}', [MedicalFacilitiesController::class, 'destroy']);

Route::post('medical-facilities/{facility_id}/units', [MedicalFacilitiesUnitController::class, 'store']);


Route::delete('medical-facilities/{facility_id}/units/{id}', [MedicalFacilitiesUnitController::class, 'destroy']);

Route::get('medical-facilities/{facility_id}/units/{id}', [MedicalFacilitiesUnitController::class, 'show']);

Route::put('medical-facilities/{facility_id}/units/{id}', [MedicalFacilitiesUnitController::class, 'update']);
