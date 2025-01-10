<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\controllers\ApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $resquest){
    return $resquest->user();
});

Route::post('login', [ApiController::class, 'login']) 
Route::post('register', [ApiController::class, 'register'])