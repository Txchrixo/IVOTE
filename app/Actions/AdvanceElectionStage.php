<?php

namespace App\Actions;

use App\Enums\ElectionStage;
use App\Exceptions\InvalidStageTransition;
use App\Models\VoteList;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;

/**
 * Moves an election to its next stage.
 * The row is locked so two concurrent requests cannot skip a stage, and each
 * transition has preconditions.
 */
class AdvanceElectionStage
{
    /**
     * @throws InvalidStageTransition
     */
    public function __invoke(VoteList $election): ElectionStage
    {
        return DB::transaction(function () use ($election) {
            $locked = VoteList::whereKey($election->getKey())->lockForUpdate()->firstOrFail();
            $current = $locked->currentStage();
            $next = $current->next() ?? throw new InvalidStageTransition('The election is already closed.');

            $this->guard($locked, $next);

            $locked->stage = $next->value;
            $locked->save();

            Audit::record('election.stage_changed', $locked, ['from' => $current->label(), 'to' => $next->label()]);

            return $next;
        });
    }

    private function guard(VoteList $election, ElectionStage $next): void
    {
        match ($next) {
            ElectionStage::Veriff => $this->requireVerificationWindow($election),
            ElectionStage::Ongoing => $this->requireVoteWindow($election),
            default => null,
        };
    }

    private function requireVerificationWindow(VoteList $election): void
    {
        if (!$election->start_veriff || !$election->end_veriff) {
            throw new InvalidStageTransition('Set the identity verification dates first.');
        }
        if ($election->candidates()->count() < 2) {
            throw new InvalidStageTransition('At least two candidates are required.');
        }
    }

    private function requireVoteWindow(VoteList $election): void
    {
        if (!$election->start_vote || !$election->end_vote) {
            throw new InvalidStageTransition('Set the voting dates first.');
        }
    }
}
