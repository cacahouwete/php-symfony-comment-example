<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Gateway\CommentGatewayInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository implements CommentGatewayInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): Comment
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
        ;
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

    /**
     * {@inheritDoc}
     */
    public function getReference(string $id): Comment
    {
        $entity = $this->getEntityManager()->getReference(Comment::class, $id);

        if (null === $entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Comment::class, ['id' => $id]);
        }

        return $entity;
    }

    public function save(Comment $entity): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function findByFilter(iterable $filters): iterable
    {
        $qb = $this->createQueryBuilder('c');

        foreach ($filters as $filter) {
            $filter->apply($qb, 'c');
        }

        return new Paginator($qb->getQuery());
    }
}
