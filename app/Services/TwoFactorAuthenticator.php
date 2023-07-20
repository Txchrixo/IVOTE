<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * TOTP (RFC 6238) second factor with replay protection and one-time recovery codes.
 */
class TwoFactorAuthenticator
{
    private const RECOVERY_CODES = 8;

    public function __construct(private readonly Google2FA $engine)
    {
        // Accept the previous and next 30 s step to absorb clock drift
        $this->engine->setWindow(1);
    }

    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey(32);
    }

    public function qrCodeSvg(string $username, string $secret): string
    {
        $url = $this->engine->getQRCodeUrl(config('app.name', 'IVote'), $username, $secret);
        $writer = new Writer(new ImageRenderer(new RendererStyle(200, 1), new SvgImageBackEnd()));

        return $writer->writeString($url);
    }

    /** Checks a code against a secret that is not stored yet (setup step). */
    public function verifySecret(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, $this->normalize($code));
    }

    /**
     * Checks a code for a user. A code (time step) can only be used once.
     */
    public function verify(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        return DB::transaction(function () use ($user, $code) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $timestep = $this->engine->verifyKeyNewer(
                $locked->two_factor_secret,
                $this->normalize($code),
                // 0 instead of null so the library always returns the matched time step
                (int) ($locked->two_factor_last_timestep ?? 0)
            );

            if ($timestep === false) {
                return false;
            }

            $locked->forceFill(['two_factor_last_timestep' => (int) $timestep])->save();

            return true;
        });
    }

    /**
     * Stores a confirmed secret and returns the plain recovery codes (shown once).
     *
     * @return array<int, string>
     */
    public function enable(User $user, string $secret): array
    {
        $codes = collect(range(1, self::RECOVERY_CODES))
            ->map(fn () => Str::upper(Str::random(5) . '-' . Str::random(5)))
            ->all();

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => array_map(fn ($code) => Hash::make($code), $codes),
            'two_factor_confirmed_at' => now(),
            'two_factor_last_timestep' => null,
        ])->save();

        return $codes;
    }

    /** Consumes a recovery code; each code works once. */
    public function useRecoveryCode(User $user, string $code): bool
    {
        $code = Str::upper(trim($code));

        return DB::transaction(function () use ($user, $code) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $hashes = $locked->two_factor_recovery_codes ?? [];

            foreach ($hashes as $index => $hash) {
                if (Hash::check($code, $hash)) {
                    unset($hashes[$index]);
                    $locked->forceFill(['two_factor_recovery_codes' => array_values($hashes)])->save();

                    return true;
                }
            }

            return false;
        });
    }

    private function normalize(string $code): string
    {
        return preg_replace('/\D/', '', $code) ?? '';
    }
}
