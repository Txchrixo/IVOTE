<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class BallotCryptography
{
    public function run(array $input): string
    {
        $process = new Process(['node', resource_path('js/crypto/verify-ballot.mjs')], base_path());
        $process->setInput(json_encode($input, JSON_THROW_ON_ERROR));
        $process->setTimeout(10);

        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            abort(503, 'The signature verifier timed out.');
        }

        abort_unless($process->isSuccessful(), 503, 'The signature verifier is unavailable.');
        return $process->getOutput();
    }
}
