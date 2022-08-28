<?php

declare(strict_types=1);

namespace App\Http\Security;

use App\Domain\User\Entity\DiscordUser;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        private IDiscordGuildService $guildService,
        private IParameterService $parameterService
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'login_discord_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('discord_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var DiscordResourceOwner $discordUser */
                $discordUser = $client->fetchUserFromToken($accessToken);

                $email = $discordUser->getEmail();

                if (
                    !$this->guildService
                        ->isGuildMember($this->parameterService->getGuildId(), (string) $discordUser->getId())
                ) {
                    throw new UserNotFoundException();
                }

                $existingUser = $this->entityManager->getRepository(DiscordUser::class)
                    ->findOneBy(['discordId' => $discordUser->getId()]);

                if ($existingUser) {
                    return $existingUser;
                }

                $user = $this->entityManager->getRepository(DiscordUser::class)->findOneBy(['email' => $email]);
                if ($user === null) {
                    $user = (new DiscordUser());
                    $user->setEmail((string) $email);
                }

                $user->setAvatar((string) $discordUser->getAvatarHash());
                $user->setUsername((string) $discordUser->getUsername());
                $user->setDiscordId((string) $discordUser->getId());
                $user->setAvatar((string) $discordUser->getAvatarHash());
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            }),
            [
                (new RememberMeBadge())->enable(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('index');
        // Set the rememberMe info for the request so isRememberMeRequested() in AbstractRememberMeServices
        // will return true
        $request->request->set('_remember_me', '1');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $this->router->generate('login');

        return new RedirectResponse($url);
    }
}
