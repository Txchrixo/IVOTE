<?php

use App\Http\Controllers\API\BallotAPIController;
use App\Http\Controllers\API\CandidateAPIController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BlockChainController;
use App\Http\Controllers\EA\EASettingController;
use App\Http\Controllers\API\ElectionAPIController;
use App\Http\Controllers\EA\VoteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PublicAPIController;
use App\Http\Controllers\RA\BallotController;
use App\Http\Controllers\RA\CandidateController;
use App\Http\Controllers\Voter\VoterController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('language/{locale}', function ($locale) {
    if (isset($locale) && in_array($locale, config('app.available_locales'))) {
        app()->setLocale($locale);
        session()->put('locale', $locale);

    }else{
        app()->setLocale(config('app.fallback_locale'));
        session()->put('locale', config('app.fallback_locale'));
    }
    return redirect()->back();
});

Route::get('/', function () {
    return auth()->check() ? to_route('auth.dashboard') : to_route('auth.login');
})->name('home');

Route::get('/cryptoapisverifydomain', function () {
    $filePath = public_path('cryptoapisverifydomain.txt');

    if (File::exists($filePath)) {
        return Response::file($filePath);
    }

    return response('File not found.', 404);
});


Route::prefix('/auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::get('/', 'getLoginForm');
    Route::get('/login', 'getLoginForm')->name('login');
    Route::post('/login', 'postLogin')->name('postLogin')->middleware('throttle:login');
    Route::get('/dashboard', 'getDashboard')->name('dashboard')->middleware(['auth', 'two-factor']);
    Route::delete('/logout', 'logOut')->name('logout')->middleware('auth');

    // Seule l'autorité électorale (EA) peut créer des comptes (RA)
    Route::get('/register', 'getRegisterForm')->name('register')->middleware(['auth', 'two-factor', 'can:ea']);
    Route::post('/register', 'postRegister')->name('postRegister')->middleware(['auth', 'two-factor', 'can:ea']);
});

// Second factor for back-office accounts (the session is not authenticated yet)
Route::prefix('/auth/two-factor')->name('auth.two-factor.')->controller(TwoFactorController::class)->group(function () {
    Route::get('/challenge', 'showChallenge')->name('challenge');
    Route::post('/challenge', 'postChallenge')->name('postChallenge')->middleware('throttle:two-factor');
    Route::get('/setup', 'showSetup')->name('setup');
    Route::post('/setup', 'postSetup')->name('postSetup')->middleware('throttle:two-factor');
    Route::get('/recovery-codes', 'showRecoveryCodes')->name('recovery-codes')->middleware('auth');
    Route::post('/cancel', 'cancel')->name('cancel');
});

Route::prefix('/auth')->name('auth.')->middleware(['auth', 'two-factor'])->controller(PasswordController::class)->group(function () {
    Route::get('/password/change', 'getChangePassword' )->name('password.change');
    Route::post('/password/change', 'postChangePassword' )->name('password.postChange');
});
Route::prefix('/veriff')->name('voter.')->group(function () {
    Route::get('/submitted', [VoterController::class, 'getVeriffSubmitted'])->name('veriffSubmitted');
    Route::get('/election/{election}', [VoterController::class, 'getVeriff'])->name('veriff');
});
Route::prefix('/voter')->name('voter.')->group(function () {
    Route::get('/ready', [VoterController::class, 'getReady'])->name('ready');
    Route::get('/generateKeys', [VoterController::class, 'getGenerateKeys'])->name('generateKeys');
    Route::get('/wait', [VoterController::class, 'getWait'])->name('wait');
    Route::get('/vote', [VoterController::class, 'getVote'])->name('vote');
});

Route::get('/api/getAllPubKeys', [PublicAPIController::class, 'getAllPubKeys'])->middleware('throttle:60,1');

Route::middleware(['auth', 'two-factor', 'can:ea'])->group(function () {
    Route::get('/ea/dashboard', [VoteController::class, 'getDashboard'])->name('ea.dashboard');


    Route::get('/ea/election', [VoteController::class, 'getElectionList'])->name('ea.election');
    Route::get('/ea/addElection', [VoteController::class, 'getAddElection'])->name('ea.addElection');
    Route::post('/ea/addElection', [VoteController::class, 'postElection']);
    Route::post('/ea/election/upload/bg-image', [VoteController::class, 'uploadBg'])->name('ea.uploadBg');

    Route::get('/ea/balance', [BlockChainController::class, 'getBalanceList'])->name('ea.balance');
    Route::get('/ea/fee', [BlockChainController::class, 'getFeeList'])->name('ea.fee');

    // Appels aux services blockchain : les clés API restent côté serveur
    Route::get('/ea/api/balance', [BlockChainController::class, 'getEABalance'])->name('ea.api.balance');
    Route::get('/api/blockio/get_balance', [BlockChainController::class, 'getBlockIoBalance']);
    Route::get('/api/blockio/get_address_balance', [BlockChainController::class, 'getBlockIoAddressBalance']);
    Route::post('/ea/api/blockio/pay', [BlockChainController::class, 'payVoterAddress'])
        ->name('ea.api.blockio.pay')->middleware('throttle:30,1');


    Route::get('/api/election/details', [ElectionAPIController::class, 'getElectionDetails']);
    Route::put('/api/election/update', [ElectionAPIController::class, 'updateElection']);
    Route::put('/api/election/toggleStage', [ElectionAPIController::class, 'toggleStage'])->middleware('throttle:10,1');
    Route::get('/api/allbitcoinaddress', [ElectionAPIController::class, 'getAllBitcoinAddress']);

    Route::get('/ea/setting', [EASettingController::class, 'getSetting'])->name('ea.setting');
    Route::post('/ea/bitcoin/setting', [EASettingController::class, 'postBitcoinSetting'])->name('ea.bitcoin.setting');
    Route::post('/ea/eth/setting', [EASettingController::class, 'postEthSetting'])->name('ea.eth.setting');
    Route::post('/ea/solana/setting', [EASettingController::class, 'postSolanaSetting'])->name('ea.solana.setting');
});

Route::middleware(['auth', 'two-factor', 'can:ra'])->group(function () {
    Route::get('/ra/dashboard', [BallotController::class, 'getDashboard'])->name('ra.dashboard');

    Route::get('/ra/addVoter', [BallotController::class, 'getAddVoter'])->name('ra.addVoter');
    Route::get('/ra/ballot', [BallotController::class, 'getBallot'])->name('ra.ballot');
    Route::post('/ra/ballot', [BallotController::class, 'postBallot']);

    Route::get('/ra/addcandidate', [CandidateController::class, 'getAddCandidate'])->name('ra.addCandidate');
    Route::post('/ra/addcandidate', [CandidateController::class, 'postAddCandidate']);

    Route::get('/ra/candidates', [CandidateController::class, 'getCandidatesList'])->name('ra.candidates');

    Route::post('/api/addvoter', [BallotAPIController::class, 'postAddVoter']);
    Route::put('/api/updatecandidate', [CandidateAPIController::class, 'postUpdateCandidate']);
    Route::delete('/api/delcandidate', [CandidateAPIController::class, 'postDelCandidate']);

});




Route::get('/vote/fail', [VoterController::class, 'getFailed'])->name('vote.fail');
Route::get('/verify/home', [VoterController::class, 'getVerifyHome'])->name('verify.home');
Route::get('/verify/now', [VoterController::class, 'getVerify'])->name('verify.now');
Route::get('/tally/home', [VoterController::class, 'getTallyHome'])->name('tally.home');
Route::get('/tally/now', [VoterController::class, 'getTally'])->name('tally.now');

