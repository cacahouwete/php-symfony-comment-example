<?php

declare(strict_types=1);

namespace App\Controller\Api\Comment;

use App\Usecases\CommentGetCollectionByFiltersUsecase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class CommentGetCollectionAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly CommentGetCollectionByFiltersUsecase $usecase,
    ) {
    }

    /**
     * Possible query params: groupKey.
     */
    #[
        Route('/api/comments', methods: ['GET']),
    ]
    public function __invoke(Request $request): Response
    {
        $collection = ($this->usecase)($request->query->all());

        return new Response($this->serializer->serialize($collection, 'json', ['groups' => ['comment:read']]));
    }
}
