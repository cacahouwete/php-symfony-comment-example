<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Rate;
use App\Gateway\RateGatewayInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Rate>
 */
class RateRepository extends ServiceEntityRepository implements RateGatewayInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function save(Rate $entity): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * @throws NoResultException
     */
    public function findByCommentAccount(Comment $comment, Account $account): Rate
    {
        return $this->createQueryBuilder('r')
            ->where('r.comment = :comment')
            ->andWhere('r.account = :account')
            ->setParameters(['comment' => $comment, 'account' => $account])
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
