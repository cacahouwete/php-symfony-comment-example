<?php

declare(strict_types=1);

namespace App\Controller\Google;

use App\Controller\ConnectAction;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

final readonly class GoogleConnectAction
{
    #[Route('/connect/google', name: 'connect_google')]
    public function __invoke(Request $request, SessionInterface $session, ClientRegistry $clientRegistry): Response
    {
        if (null === $session->get(ConnectAction::SESSION_AUTH_REDIRECT)) {
            $session->set(ConnectAction::SESSION_AUTH_REDIRECT, $request->headers->get('referer'));
        }

        // Redirect to google
        return $clientRegistry->getClient('google')->redirect([], []);
    }
}
