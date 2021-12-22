<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Domain\User\Entity\User;
use App\Infrastructure\Discord\Entity\DiscordRole;
use App\Infrastructure\Parameter\IParameterService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordUserService implements IDiscordUserService
{
    private const CACHE_KEY_USER_ROLES = 'user_roles_';
    private const EXPIRE_USER_ROLES = 3600;

    public function __construct(
        private IParameterService $parameterService,
        private IDiscordGuildService $guildService,
        private HttpClientInterface $discordClient,
        private CacheItemPoolInterface $cache
    ) {
    }

    public function getAvatarUser(User $user): string
    {
        return "https://cdn.discordapp.com/avatars/{$user->getDiscordId()}/{$user->getAvatar()}.png";
    }

    /** {@inheritDoc} */
    public function getRoles(User $user): array
    {
        $i = $this->cache->getItem(self::CACHE_KEY_USER_ROLES . $user->getDiscordId());
        if (!$i->isHit()) {
            try {
                $response = $this->discordClient->request(Request::METHOD_GET, 'guilds/' . $this->parameterService->getGuildId() . '/members/' . $user->getDiscordId());
            } catch (TransportExceptionInterface $e) {
                return [];
            }
            $userRoles = json_decode($response->getContent(false))->roles;

            $i->set(array_filter($this->guildService->getRoles(), fn (DiscordRole $r) => \in_array((string) ($r->getId()), $userRoles, true)));

            $i->expiresAfter(self::EXPIRE_USER_ROLES);
            $this->cache->save($i);
        }

        return $i->get();
    }
}
