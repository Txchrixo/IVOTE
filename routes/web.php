<?php

use App\Http\Controllers\API\BallotAPIController;
use App\Http\Controllers\API\CandidateAPIController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\BlockChainController;
use App\Http\Controllers\EA\EASettingController;
use App\Http\Controllers\EA\ElectionAPIController;
use App\Http\Controllers\EA\VoteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\API\PublicAPIController;
use App\Http\Controllers\RA\BallotController;
use App\Http\Controllers\RA\CandidateController;
use App\Http\Controllers\Voter\VoterController;
use App\Http\Middleware\EAMiddleware;
use App\Http\Middleware\RAMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
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
Route::get('/cryptoapisverifydomain', function () {
    $filePath = public_path('cryptoapisverifydomain.txt');

    if (File::exists($filePath)) {
        return Response::file($filePath);
    }

    return response('File not found.', 404);
});
Route::get('img/{filename}', function ($filename) {
    return response()->redirectTo('http://[::1]:5173/resources/img/' . $filename);
})->where('filename', '.*');



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::get('/', 'getLoginForm' );
    Route::get('/dashboard', 'getDashboard' )->name('dashboard')->middleware('auth');
    Route::get('/login', 'getLoginForm')->name('login');
    Route::post('/login', 'postLogin')->name('postLogin');
    Route::get('/register', 'getRegisterForm')->name('register');
    Route::post('/register', 'postRegister')->name('postRegister');
    Route::delete('/logout', 'logOut')->name('logout');
});

Route::prefix('/cryptoapis-proxy')->name('cryptoapis.')->group(function () {
    Route::get('/blockchain-data/{blockchain}/{network}/addresses/{address}/balance', function (Request $request, $blockchain, $network, $address) {
        $url = 'https://rest.cryptoapis.io/blockchain-data/' . $blockchain . '/' . $network . '/addresses/' . $address . '/balance';

        // Ajoutez les en-têtes CORS appropriés pour autoriser les requêtes cross-origin depuis votre domaine local
        return Http::withHeaders([
            'Access-Control-Allow-Origin' => 'http://localhost:8000',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, Accept',
            'x-api-key' => 'REDACTED_CRYPTOAPIS_KEY'
        ])->get($url);
    });
});


Route::prefix('/auth')->name('auth.')->controller(PasswordController::class)->group(function () {
    Route::get('/password/change', 'getChangePassword' )->name('password.change');
    Route::post('/password/change', 'postChangePassword' )->name('password.postChange');
});
Route::prefix('/veriff')->name('voter.')->group(function () {
    Route::get('/submitted', [VoterController::class, 'getVeriffSubmitted'])->name('veriffSubmitted');
    Route::get('/{election}', [VoterController::class, 'getVeriff'])->name('veriff');
});
Route::prefix('/voter')->name('voter.')->group(function () {
    Route::get('/ready', [VoterController::class, 'getReady'])->name('ready');
    Route::get('/generateKeys', [VoterController::class, 'getGenerateKeys'])->name('generateKeys');
    Route::get('/wait', [VoterController::class, 'getWait'])->name('wait');
    Route::get('/vote', [VoterController::class, 'getVote'])->name('vote');
    //Route::get('/{election}', [VoterController::class, 'getVeriff'])->name('veriff');
});

Route::get('/encrypt-data', [VoterController::class, 'encryptData'])->name('encrypt');
Route::get('/decrypt-data', [VoterController::class, 'decryptData'])->name('decrypt');

Route::get('/api/blockio/get_balance', [PublicAPIController::class, 'getBalance']);
Route::get('/api/blockio/get_address_balance', [PublicAPIController::class, 'getAddressBalance']);
Route::get('/api/getAllPubKeys', [PublicAPIController::class, 'getAllPubKeys']);
Route::post('/postVeriffParams', [PublicAPIController::class, 'postVeriffParams']);

Route::middleware(EAMiddleware::class)->group( function () {
    Route::get('/ea/dashboard', [VoteController::class, 'getDashboard'])->name('ea.dashboard');


    Route::get('/ea/election', [VoteController::class, 'getElectionList'])->name('ea.election');
    Route::get('/ea/addElection', [VoteController::class, 'getAddElection'])->name('ea.addElection');
    Route::post('/ea/addElection', [VoteController::class, 'postElection']);
    Route::post('/ea/election/upload/bg-image', [VoteController::class, 'uploadBg'])->name('ea.uploadBg');

    Route::get('/ea/balance', [BlockChainController::class, 'getBalanceList'])->name('ea.balance');
    Route::get('/ea/fee', [BlockChainController::class, 'getFeeList'])->name('ea.fee');


    Route::get('/ea/setting', [EASettingController::class, 'getSetting'])->name('ea.setting');
    Route::post('/ea/bitcoin/setting', [EASettingController::class, 'postBitcoinSetting'])->name('ea.bitcoin.setting');
    Route::post('/ea/eth/setting', [EASettingController::class, 'postEthSetting'])->name('ea.eth.setting');
    Route::post('/ea/solana/setting', [EASettingController::class, 'postSolanaSetting'])->name('ea.solana.setting');
});

Route::middleware(RAMiddleware::class)->group( function () {
    Route::get('/ra/dashboard', [BallotController::class, 'getDashboard'])->name('ra.dashboard');

    Route::get('/ra/addVoter', [BallotController::class, 'getAddVoter'])->name('ra.addVoter');
    Route::get('/ra/ballot', [BallotController::class, 'getBallot'])->name('ra.ballot');
    Route::post('/ra/ballot', [BallotController::class, 'postBallot']);

    Route::get('/ra/addcandidate', [CandidateController::class, 'getAddCandidate'])->name('ra.addCandidate');
    Route::post('/ra/addcandidate', [CandidateController::class, 'postAddCandidate']);

    Route::get('/ra/candidates', [CandidateController::class, 'getCandidatesList'])->name('ra.candidates');

    Route::post('/api/addvoter', [BallotAPIController::class, 'postAddVoter']);
    Route::post('/api/addcandidate', [CandidateAPIController::class, 'postAddCandidate']);
    Route::put('/api/updatecandidate', [CandidateAPIController::class, 'postUpdateCandidate']);
    Route::delete('/api/delcandidate', [CandidateAPIController::class, 'postDelCandidate']);

});




Route::get('/vote/fail', [VoterController::class, 'getFailed'])->name('vote.fail');
Route::get('/verify/home', [VoterController::class, 'getVerifyHome'])->name('verify.home');
Route::get('/verify/now', [VoterController::class, 'getVerify'])->name('verify.now');
Route::get('/tally/home', [VoterController::class, 'getTallyHome'])->name('tally.home');
Route::get('/tally/now', [VoterController::class, 'getTally'])->name('tally.now');

