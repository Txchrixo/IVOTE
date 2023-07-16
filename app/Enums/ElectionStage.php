<?php

namespace App\Enums;

/**
 * Lifecycle of an election. Stages only move forward, one step at a time.
 */
enum ElectionStage: int
{
    case Initial = 0;
    case Veriff = 1;
    case Waiting = 2;
    case Ongoing = 3;
    case Done = 4;

    public function next(): ?self
    {
        return self::tryFrom($this->value + 1);
    }

    public function label(): string
    {
        return strtolower($this->name);
    }
}
