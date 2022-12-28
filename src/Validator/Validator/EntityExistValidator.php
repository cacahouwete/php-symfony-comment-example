<?php

declare(strict_types=1);

namespace App\Validator\Validator;

use App\Validator\Constraints\EntityExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class EntityExistValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExist) {
            throw new UnexpectedTypeException($constraint, EntityExist::class);
        }

        if (null === $constraint->className) {
            throw new \UnexpectedValueException('EntityExist::className must be provided');
        }

        if (!class_exists($constraint->className)) {
            throw new \UnexpectedValueException("className({$constraint->className} not exist");
        }

        if (null === $value) {
            return;
        }
        $entityRepository = $this->entityManager->getRepository($constraint->className);
        if (\is_array($value)) {
            foreach ($value as $key => $id) {
                $object = $entityRepository->find($id);

                if (null === $object) {
                    $this->context->buildViolation($constraint->message)
                        ->atPath((string) $key)
                        ->setParameter('{{ className }}', $constraint->className)
                        ->setParameter('{{ value }}', (string) $id)
                        ->setCode($constraint->code)
                        ->addViolation();
                }
            }
        } elseif (\is_string($value)) {
            if ('' === $value) {
                return;
            }

            $object = $entityRepository->find($value);

            if (null === $object) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ className }}', $constraint->className)
                    ->setParameter('{{ value }}', $value)
                    ->setCode($constraint->code)
                    ->addViolation();
            }
        } else {
            throw new UnexpectedValueException($value, 'string|array');
        }
    }
}
