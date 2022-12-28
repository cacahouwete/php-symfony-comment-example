<?php

declare(strict_types=1);

namespace App\Security;

use App\Controller\ConnectAction;
use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return 'connect_google_check' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                // have they logged in with Google before? Easy!
                $existingUser = $this->entityManager->getRepository(Account::class)->findOneBy(['googleId' => $googleUser->getId()]);
                if (null !== $existingUser) {
                    return $existingUser;
                }

                if (null === $email) {
                    return null;
                }

                $existingUser = $this->entityManager->getRepository(Account::class)->findOneBy(['email' => $email]);
                if (null === $existingUser) {
                    // User doesnt exist, we create it !
                    $existingUser = new Account($googleUser->getFirstName() ?? random_bytes(6), $email);
                    $this->entityManager->persist($existingUser);
                }
                $existingUser->setGoogleId(\is_string($googleUser->getId()) ? $googleUser->getId() : null);
                $existingUser->setHostedDomain($googleUser->getHostedDomain());
                $existingUser->setAvatar($googleUser->getAvatar());

                $this->entityManager->flush();

                return $existingUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $authRedirect = $request->getSession()->get(ConnectAction::SESSION_AUTH_REDIRECT);
        if (\is_string($authRedirect)) {
            $request->getSession()->remove(ConnectAction::SESSION_AUTH_REDIRECT);
        } else {
            $authRedirect = $this->router->generate('app_homepageaction__invoke');
        }

        return new RedirectResponse($authRedirect);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
