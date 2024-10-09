<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Spatie\RouteDiscovery\Discovery\Discover;

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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/user-info', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Discover::controllers()->in(app_path('Http/Controllers/Crud'));
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Request reset password link
Route::post('password/send-reset-link-email', [AuthController::class, 'sendResetLinkEmail']);

// Reset password
Route::post('password/reset', [AuthController::class, 'resetPassword']);

// Route untuk mengirim ulang verifikasi email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent!']);
})->middleware('auth:sanctum')->name('verification.send');

// Route untuk verifikasi email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
