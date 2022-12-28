<?php

declare(strict_types=1);

namespace App\Gateway;

use App\Entity\Account;
use Doctrine\ORM\EntityNotFoundException;

interface AccountGatewayInterface
{
    /**
     * @throws EntityNotFoundException
     */
    public function getReference(string $id): Account;

    public function exist(string $id): bool;

    public function save(Account $entity): void;
}
