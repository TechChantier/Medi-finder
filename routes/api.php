<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController; 
use App\Http\Controllers\MedicalFacilitiesController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [ApiController::class, 'login']);
Route::post('register', [ApiController::class, 'register']);


Route::group(['middleware' => 'auth:sanctum'], function(){

    Route::post('logout', [ApiController::class, 'logout']);

});
Route::post('medical-facilities', [MedicalFacilitiesController::class, 'store']);
Route::get('medical-facilities/{id}', [MedicalFacilitiesController::class, 'show']);
 // Delete a medical facility
 Route::delete('medical-facilities/{id}', [MedicalFacilitiesController::class, 'destroy']);
