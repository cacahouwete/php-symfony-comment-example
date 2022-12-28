<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final readonly class ConnectAction
{
    public const SESSION_AUTH_REDIRECT = 'auth_redirect';

    #[
        Route('/connect', methods: ['GET']),
    ]
    public function __invoke(Request $request, SessionInterface $session, Environment $twig): Response
    {
        $session->set(self::SESSION_AUTH_REDIRECT, $request->headers->get('referer'));

        return new Response($twig->render('connect/index.html.twig'));
    }
}
