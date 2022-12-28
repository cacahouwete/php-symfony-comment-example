<?php

declare(strict_types=1);

namespace App\Usecases;

use App\Dto\CommentUpdate;
use App\Entity\Account;
use App\Entity\Comment;
use App\Gateway\CommentGatewayInterface;
use App\Gateway\RateGatewayInterface;
use Doctrine\ORM\NoResultException;

final readonly class CommentUpdateUsecase
{
    public function __construct(
        private readonly CommentGatewayInterface $commentGateway,
        private readonly RateGatewayInterface $rateGateway,
    ) {
    }

    public function __invoke(string $id, CommentUpdate $payload, Account $account): Comment
    {
        $comment = $this->commentGateway->findById($id);

        if (null !== $payload->rate) {
            $this->addOrUpdateRate($comment, $account, (float) $payload->rate);
        }

        $this->commentGateway->save($comment);

        return $comment;
    }

    private function addOrUpdateRate(Comment $comment, Account $account, float $rate): void
    {
        try {
            $entity = $this->rateGateway->findByCommentAccount($comment, $account);
            $entity->setValue($rate);
        } catch (NoResultException) {
            $comment->addRate($account, $rate);
        }
    }
}
