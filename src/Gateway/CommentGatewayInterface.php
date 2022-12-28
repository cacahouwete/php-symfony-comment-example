<?php

declare(strict_types=1);

namespace App\Gateway;

use App\Entity\Comment;
use App\Filters\FilterWithValue;
use Doctrine\ORM\EntityNotFoundException;

interface CommentGatewayInterface
{
    /**
     * @param iterable<FilterWithValue> $filters
     *
     * @return iterable<Comment>
     */
    public function findByFilter(iterable $filters): iterable;

    /**
     * @throws EntityNotFoundException
     */
    public function findById(string $id): Comment;

    /**
     * @throws EntityNotFoundException
     */
    public function getReference(string $id): Comment;

    public function exist(string $id): bool;

    public function save(Comment $entity): void;
}
