<?php

declare(strict_types=1);

namespace App\Serializer;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final readonly class PaginatorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(readonly ObjectNormalizer $normalizer)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Paginator) {
            throw new UnexpectedValueException();
        }
        $items = [];
        foreach ($object as $item) {
            $items[] = $this->normalizer->normalize($item, $format, $context);
        }

        $page = null;
        $itemsPerPage = $object->getQuery()->getMaxResults();
        if (null !== $itemsPerPage) {
            $page = ((int) $object->getQuery()->getFirstResult() / $itemsPerPage) + 1;
        }

        return [
            'items' => $items,
            'totalItems' => \count($object),
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Paginator;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
