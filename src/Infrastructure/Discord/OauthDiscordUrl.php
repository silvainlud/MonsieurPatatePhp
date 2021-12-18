<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OauthDiscordUrl
{
    public function __construct(private string $clientId, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function getUrlLogin(): string
    {
        $redirect = urlencode($this->urlGenerator->generate('login_discord_check', referenceType: UrlGeneratorInterface::ABSOLUTE_URL));

        return "https://discord.com/api/oauth2/authorize?client_id={$this->clientId}&redirect_uri={$redirect}&response_type=code&scope=identify%20email";
    }
}
