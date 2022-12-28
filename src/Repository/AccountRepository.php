<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use App\Gateway\AccountGatewayInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository implements AccountGatewayInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function exist(string $id): bool
    {
        return $this->createQueryBuilder('COUNT(c)')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }

    public function getReference(string $id): Account
    {
        $entity = $this->getEntityManager()->getReference(Account::class, $id);

        if (null === $entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Account::class, ['id' => $id]);
        }

        return $entity;
    }

    public function save(Account $entity): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($entity);
        $entityManager->flush();
    }
}
