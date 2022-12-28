<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Validator\Validator\EntityExistValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class EntityExist extends Constraint
{
    public string $message = 'Entity {{ className }} with id {{ value }} does not exist.';
    public string $code = 'EntityExist';
    public ?string $className = null;

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed>|string|null $className
     * @param array<string, mixed>             $options
     */
    public function __construct($className, string $message = null, array $groups = null, $payload = null, array $options = [])
    {
        if (\is_array($className) && \is_string(key($className))) {
            $options = array_merge($className, $options);
        } elseif (null !== $className) {
            $options['value'] = $className;
        }

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return EntityExistValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption(): string
    {
        return 'className';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['className'];
    }
}
