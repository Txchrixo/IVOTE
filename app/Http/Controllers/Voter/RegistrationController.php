<?php

namespace App\Http\Controllers\Voter;

use App\Enums\ElectionStage;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\VoteList;
use App\Services\BallotCryptography;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    private const CODE = ['required', 'regex:/\A[A-Za-z0-9]{1,64}\z/'];

    public function register(Request $request, VoteList $election, BallotCryptography $crypto): RedirectResponse
    {
        $data = $request->validate([
            'code' => self::CODE,
            'public_key' => ['required', 'regex:/\A0[23][a-fA-F0-9]{64}\z/'],
            'proof' => ['required', 'regex:/\A[a-f0-9]{128}\z/'],
        ]);
        $publicKey = $crypto->run([
            'publicKey' => $data['public_key'], 'proof' => $data['proof'],
            'election' => $election->id, 'code' => $data['code'],
        ]);
        if ($publicKey === '') {
            throw ValidationException::withMessages(['public_key' => 'The voting key could not be verified.']);
        }

        try {
            DB::transaction(function () use ($election, $data, $publicKey) {
                $code = $this->unusedCode($election, $data['code']);
                if (config('services.veriff.api_key') && !$code->identity_verified_at) {
                    throw ValidationException::withMessages(['code' => 'Veriff approval is still required.']);
                }
                if (Code::where('public_key', $publicKey)->exists()) {
                    throw ValidationException::withMessages(['public_key' => 'Generate a fresh key for this election.']);
                }
                $code->forceFill(['public_key' => $publicKey, 'registered_key' => $publicKey, 'is_used' => true])->save();
            });
        } catch (QueryException $exception) {
            if (!Code::where('registered_key', $publicKey)->exists()) {
                throw $exception;
            }
            throw ValidationException::withMessages(['public_key' => 'Generate a fresh key for this election.']);
        }

        return to_route('voter.wait', ['election' => $election->id])->with('success', 'Your voting key is registered.');
    }

    public function startIdentity(Request $request, VoteList $election): RedirectResponse
    {
        $apiKey = config('services.veriff.api_key');
        abort_unless($apiKey && config('services.veriff.secret'), 503, 'Veriff is unavailable.');
        $data = $request->validate(['code' => self::CODE]);

        return DB::transaction(function () use ($election, $apiKey, $data) {
            $code = $this->unusedCode($election, $data['code']);
            if ($code->identity_verified_at) {
                return to_route('voter.generateKeys', ['election' => $election->id]);
            }
            if (!$code->verification_url) {
                $reference = (string) Str::uuid();
                $verification = [
                    'vendorData' => $reference,
                    'callback' => route('voter.generateKeys', ['election' => $election->id]),
                ];
                try {
                    $response = Http::acceptJson()->withHeaders(['X-AUTH-CLIENT' => $apiKey])->timeout(8)
                        ->post(rtrim(config('services.veriff.host'), '/') . '/v1/sessions', ['verification' => $verification]);
                } catch (ConnectionException) {
                    abort(502, 'Veriff is unavailable.');
                }
                $session = $response->json('verification.id');
                $url = $response->json('verification.url');
                abort_unless(
                    $response->successful() && is_string($session) && Str::isUuid($session)
                        && is_string($url) && str_starts_with($url, 'https://') && filter_var($url, FILTER_VALIDATE_URL),
                    502,
                    'Veriff did not create a session.'
                );
                $code->forceFill([
                    'verification_reference' => $reference, 'verification_session' => $session, 'verification_url' => $url,
                ])->save();
            }

            return redirect()->away($code->verification_url);
        });
    }

    public function decision(Request $request): Response
    {
        $secret = (string) config('services.veriff.secret');
        $apiKey = (string) config('services.veriff.api_key');
        abort_unless(
            $secret !== '' && $apiKey !== '' && hash_equals($apiKey, (string) $request->header('X-AUTH-CLIENT'))
                && hash_equals(hash_hmac('sha256', $request->getContent(), $secret), (string) $request->header('X-HMAC-SIGNATURE')),
            401
        );

        $data = $request->validate([
            'verification.id' => ['required', 'uuid'],
            'verification.vendorData' => ['required', 'uuid'],
            'verification.status' => ['required', 'string'],
        ]);
        $verification = $data['verification'];
        $code = Code::where('verification_reference', $verification['vendorData'])
            ->where('verification_session', $verification['id'])->whereNull('identity_verified_at');

        if ($verification['status'] === 'approved') {
            $code->update(['identity_verified_at' => now()]);
        } elseif (in_array($verification['status'], ['expired', 'abandoned'], true)) {
            $code->update(['verification_reference' => null, 'verification_session' => null, 'verification_url' => null]);
        }

        return response()->noContent();
    }

    private function unusedCode(VoteList $election, string $value): Code
    {
        $election = VoteList::whereKey($election->id)->lockForUpdate()->firstOrFail();
        abort_unless($election->currentStage() === ElectionStage::Veriff, 409, 'Voter registration is closed.');
        $code = Code::where('item_id', $election->id)->where('code', strtoupper($value))->lockForUpdate()->first();
        if (!$code || $code->is_used || ($code->public_key && $code->public_key !== '0')) {
            throw ValidationException::withMessages(['code' => 'This registration code is invalid or already used.']);
        }

        return $code;
    }
}
