<?php

declare(strict_types=1);

namespace App\Usecases;

use App\Dto\CommentCreate;
use App\Entity\Account;
use App\Entity\Comment;
use App\Gateway\CommentGatewayInterface;

final readonly class CommentCreateUsecase
{
    public function __construct(private readonly CommentGatewayInterface $commentGateway)
    {
    }

    public function __invoke(CommentCreate $commentCreate, Account $author): Comment
    {
        $parent = null;

        if (null !== $commentCreate->parentId) {
            $parent = $this->commentGateway->getReference($commentCreate->parentId);
        }

        $entity = new Comment(
            null,
            $commentCreate->groupKey,
            $commentCreate->content,
            $author,
            $parent,
        );

        $this->commentGateway->save($entity);

        return $entity;
    }
}
