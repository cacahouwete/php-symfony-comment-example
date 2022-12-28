<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

final readonly class LogoutAction
{
    #[
        Route('/logout', name: 'app_logout', methods: ['GET']),
    ]
    public function __invoke(): void
    {
    }
}
