<?php

use App\Http\Controllers\CitizenController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MinistryBranchController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserOTPController;
use Illuminate\Support\Facades\Route;

Route::post('sign-up', [UserController::class, 'signUp']);
Route::post('login', [UserController::class, 'login']);
Route::post('refresh-token', [UserController::class, 'refreshToken']);

Route::post('verify-otp', [UserOTPController::class, 'verifyOtp']);
Route::post('resend-otp', [UserOTPController::class, 'resendOtp']);

Route::prefix('complaint')->middleware(['auth:sanctum', 'active.user'])->controller(ComplaintController::class)->group(function () {
    Route::post('submit', 'submit');
    Route::get('get-complaints/{ministry_branch_id}', 'getComplaints')->middleware('permission:complaint.read');
    Route::get('get-complaint/{complaint_id}', 'getComplaint')->middleware('permission:complaint.read');
    Route::get('get-my-complaints', 'getMyComplaints');
});

Route::get('get-governorates', [MinistryController::class, 'getGovernorates'])->middleware(['auth:sanctum', 'active.user']);

Route::prefix('ministry')->middleware(['auth:sanctum', 'active.user'])->group(function () {
    Route::controller(MinistryController::class)->group(function () {
        Route::post('add', 'add')->middleware('role:super_admin');
        Route::get('get-ministries', 'getMinistries');
        Route::get('get-info/{ministry_id}', 'getMinistryInfo');
    });

    Route::controller(MinistryBranchController::class)->group(function () {
        Route::post('branches/add', 'add')->middleware('role:super_admin');
        Route::get('branches/{ministry_id}', 'getBranches');
    });
});

Route::prefix('employee')->middleware(['auth:sanctum', 'active.user'])->controller(EmployeeController::class)->group(function () {
    Route::post('add', 'add')->middleware('permission:employee.create');
    Route::get('get-employees', 'getEmployees')->middleware('permission:employee.read');
    Route::get('get-employees/{ministry_branch_id}', 'getEmployeesInBranch')->middleware('permission:employee.read');

    Route::post('promote-employee/{employee_id}', 'promoteEmployee')->middleware('permission:employee.update');
});

Route::prefix('citizen')->middleware(['auth:sanctum', 'active.user'])->controller(CitizenController::class)->group(function () {
    Route::post('complete-info', 'completeInfo');
});
