<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerifyController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('email/verify/{id}/{hash}', [VerifyController::class, 'verify'])->middleware('signed')->name('verification.verify');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('email/resend', [VerifyController::class, 'resend'])->name('verification.resend');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::group(['middleware' => 'verfiedApi'], function () {
        Route::get('/user/getMe', [AuthController::class,'getMe'])->name('user.me');
    });
});
