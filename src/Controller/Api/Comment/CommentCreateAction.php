<?php

declare(strict_types=1);

namespace App\Controller\Api\Comment;

use App\Dto\CommentCreate;
use App\Entity\Account;
use App\Usecases\CommentCreateUsecase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnexpectedSessionUsageException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CommentCreateAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly CommentCreateUsecase $usecase,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    #[
        Route('/api/comments', methods: ['POST']),
        IsGranted('IS_AUTHENTICATED_REMEMBERED'),
    ]
    public function __invoke(Request $request): Response
    {
        $payload = $this->serializer->deserialize($request->getContent(), CommentCreate::class, 'json');

        $violations = $this->validator->validate($payload);

        if (\count($violations) > 0) {
            return new Response(
                $this->serializer->serialize($violations, 'json'),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $currentUser = $this->tokenStorage->getToken()?->getUser();
        if (!$currentUser instanceof Account) {
            throw new UnexpectedSessionUsageException();
        }

        $entity = ($this->usecase)($payload, $currentUser);

        return new Response($this->serializer->serialize($entity, 'json', ['groups' => ['comment:read']]));
    }
}
