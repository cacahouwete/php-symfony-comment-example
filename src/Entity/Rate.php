<?php

declare(strict_types=1);

namespace App\Entity;

class Rate
{
    private Account $account;
    private Comment $comment;
    private float $value;

    public function __construct(Account $account, Comment $comment, float $value)
    {
        $this->account = $account;
        $this->comment = $comment;
        if ($value < 0. || $value > 5.) {
            throw new \UnexpectedValueException('Value should be between <0,5>');
        }
        $this->value = $value;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        if ($value < 0. || $value > 5.) {
            throw new \UnexpectedValueException('Value should be between <0,5>');
        }
        $oldValue = $this->value;
        $this->value = $value;
        $this->comment->rateUpdated($value, $oldValue);
    }
}
