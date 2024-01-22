<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'userRegistration']);
Route::post('/login', [UserController::class, 'userLogin']);
//Route::post('/logout', [UserController::class, 'userLogout']);
Route::post('/send-otp', [UserController::class, 'sentOTP']);
Route::post('/verify-otp', [UserController::class, 'verifyOTP']);
Route::post('/reset-password', [UserController::class, 'resetPassword'])->middleware('token.verification');
Route::get('/user/profile', [UserController::class, 'getUserProfile'])->middleware('token.verification');
Route::post('/user/edit-profile', [UserController::class, 'updateUserProfile'])->middleware('token.verification');

