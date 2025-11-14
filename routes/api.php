<?php

use App\Http\Controllers\CitizenController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserOTPController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('sign-up', [UserController::class, 'signUp']);
Route::post('login', [UserController::class, 'login']);

Route::post('verify-otp', [UserOTPController::class, 'verifyOtp']);
Route::post('resend-otp', [UserOTPController::class, 'resendOtp']);

Route::prefix('citizen')->group(function () {});

Route::prefix('ministry')->controller(MinistryController::class)->group(function () {
    Route::post('add', 'add');
});
