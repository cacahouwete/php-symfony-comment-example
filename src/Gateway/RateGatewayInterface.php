<?php

declare(strict_types=1);

namespace App\Gateway;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Rate;
use Doctrine\ORM\NoResultException;

interface RateGatewayInterface
{
    /**
     * @throws NoResultException
     */
    public function findByCommentAccount(Comment $comment, Account $account): Rate;

    public function save(Rate $entity): void;
}
