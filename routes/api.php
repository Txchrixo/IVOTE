<?php

use App\Http\Controllers\API\CandidateAPIController;
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

// Les routes EA (election/details, election/update, election/toggleStage, allbitcoinaddress)
// sont dans routes/web.php : elles ont besoin de la session et de la protection CSRF.
