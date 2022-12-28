<?php

declare(strict_types=1);

namespace App\Controller\Google;

use Symfony\Component\Routing\Annotation\Route;

final readonly class GoogleConnectCheckAction
{
    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml.
     */
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function __invoke(): void
    {
    }
}
