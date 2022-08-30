<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\AuthController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes.........
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'index');
        Route::post('/user/create', 'store');
        Route::get('/user/{id}', 'show');
        Route::post('/user/update/{id}', 'update');
        Route::delete('/user/delete/{id}', 'destroy');
    });

    // Outlet routes....
    Route::controller(OutletController::class)->group(function () {
        Route::get('/outlet', 'index');
        Route::post('/outlet/create', 'store');
        Route::get('/outlet/{id}', 'show');
        Route::post('/outlet/update/{id}', 'update');
        Route::delete('/outlet/delete/{id}', 'destroy');
        // Location routes........
        Route::get('/outlet-location/{outlet_id}', 'getOutletLocation');
    });
});
