<?php

use App\Http\Controllers\api\ApplicationController;
use App\Http\Controllers\api\CandidateController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobListController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/jobs', [JobListController::class, 'index']);

// Protected Route (Wajib Bawa Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::post('/jobs', [JobListController::class, 'store']);
    Route::put('/jobs/{id}', [JobListController::class, 'update']);
    Route::delete('/jobs/{id}', [JobListController::class, 'destroy']);

    Route::get('/candidates', [CandidateController::class, 'index']);
    Route::post('/candidates', [CandidateController::class, 'store']);
    Route::put('/candidates/{id}', [CandidateController::class, 'update']);
    Route::get('/candidates/{id}', [CandidateController::class, 'show']);

    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::post('/applications', [ApplicationController::class, 'store']);
});


