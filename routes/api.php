<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/





Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/forgot-password',[AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']); 

// ==================== USER ROUTES (Protected) ====================
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Profile & Installment
    Route::get('/user/profile', [ProfileApiController::class, 'profile']);
    Route::post('/user/profile', [ProfileApiController::class, 'profileUpdate']);
    
    Route::get('/user/installments', [ProfileApiController::class, 'getInstallments']);
    Route::post('/user/installments', [ProfileApiController::class, 'addInstallment']);
    Route::put('/user/installments', [ProfileApiController::class, 'updateInstallment']);
    Route::delete('/user/installments/{id}', [ProfileApiController::class, 'deleteInstallment']);
});