<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// *******************************Auth***********************************
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function (){

    Route::post('/order', [OrderController::class, 'createOrder'])->middleware(['captain']);
    Route::get('/orders', [OrderController::class, 'orders'])->middleware(['captain']);
    Route::post('/receiveItems', [OrderController::class, 'receiveItemsByDepartment'])->middleware(['chief']);

});
