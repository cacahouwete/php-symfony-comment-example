<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final readonly class HomePageAction
{
    public function __construct(private readonly Environment $twig)
    {
    }

    #[
        Route('/', methods: ['GET']),
    ]
    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render('home_page/index.html.twig')
        );
    }
}
