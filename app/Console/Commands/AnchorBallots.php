<?php

namespace App\Console\Commands;

use App\Models\VoteList;
use App\Services\BitcoinBallotAnchor;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use RuntimeException;

class AnchorBallots extends Command
{
    protected $signature = 'ivote:anchor {election : Election ID}';

    protected $description = 'Publish ballot receipts to Bitcoin testnet or regtest';

    public function handle(BitcoinBallotAnchor $anchor): int
    {
        $election = VoteList::find($this->argument('election'));
        if (!$election || !($election->ballot_definition['bitcoin'] ?? false)) {
            $this->error('This election was not opened in Bitcoin mode.');
            return self::FAILURE;
        }

        $confirmed = 0;
        $pending = 0;
        try {
            $anchor->checkNetwork();
            foreach ($election->ballots()->lazyById() as $ballot) {
                $anchor->publish($ballot) ? $confirmed++ : $pending++;
            }
        } catch (RuntimeException|LockTimeoutException $exception) {
            $this->error($exception->getMessage() ?: 'The wallet is busy. Retry later.');
            return self::FAILURE;
        }

        $this->info("Confirmed: {$confirmed}. Pending: {$pending}.");
        return self::SUCCESS;
    }
}
