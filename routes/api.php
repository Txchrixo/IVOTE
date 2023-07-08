<?php

use App\Http\Controllers\API\CandidateAPIController;
use App\Http\Controllers\API\ElectionAPIController;
use App\Http\Middleware\EAMiddleware;
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

// Public API
Route::get('candidates', [CandidateAPIController::class, 'getCandidates']);

Route::middleware(EAMiddleware::class)->group(function () {
    Route::get('election/details', [ElectionAPIController::class, 'getElectionDetails']);
    Route::put('election/update', [ElectionAPIController::class, 'updateElection']);
    Route::put('election/toggleStage', [ElectionAPIController::class, 'toggleStage']);
    Route::get('allbitcoinaddress', [ElectionAPIController::class, 'getAllBitcoinAddress']);
});

