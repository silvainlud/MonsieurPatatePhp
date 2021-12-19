<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Infrastructure\Discord\Entity\DiscordGuild;
use App\Infrastructure\Discord\Entity\DiscordRole;
use App\Infrastructure\Parameter\IParameterService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordGuildService implements IDiscordGuildService
{
    private const CACHE_KEY_CURRENT_GUILD = 'current_guild';
    private const EXPIRE_CURRENT_GUILD = 259200;

    private const CACHE_KEY_CURRENT_GUILD_ROLES = 'current_guild_roles';
    private const EXPIRE_CURRENT_GUILD_ROLES = 259200;

    public function __construct(
        private HttpClientInterface $discordClient,
        private CacheItemPoolInterface $cache,
        private IParameterService $parameterService
    ) {
    }

    public function getCurrentGuild(): DiscordGuild
    {
        $i = $this->cache->getItem(self::CACHE_KEY_CURRENT_GUILD);
        if (!$i->isHit()) {
            try {
                $response = $this->discordClient->request(Request::METHOD_GET, 'guilds/' . $this->parameterService->getGuildId());
            } catch (TransportExceptionInterface $e) {
                return (new DiscordGuild(0))->setName('Error');
            }

            $data = json_decode($response->getContent(false));
            $guild = (new DiscordGuild($data->id))->setName($data->name)->setIcon($data->icon);
            $i->set($guild);
            $i->expiresAfter(self::EXPIRE_CURRENT_GUILD);
            $this->cache->save($i);
        }

        return $i->get();
    }

    public function getCurrentGuildIcon(): string
    {
        return "https://cdn.discordapp.com/icons/{$this->getCurrentGuild()->getId()}/{$this->getCurrentGuild()->getIcon()}.png";
    }

    /** {@inheritDoc} */
    public function getRoles(): array
    {
        $i = $this->cache->getItem(self::CACHE_KEY_CURRENT_GUILD_ROLES);
        if (!$i->isHit()) {
            try {
                $response = $this->discordClient->request(Request::METHOD_GET, 'guilds/' . $this->parameterService->getGuildId() . '/roles');
            } catch (TransportExceptionInterface $e) {
                return [];
            }
            $data = array_reduce(json_decode($response->getContent(false)), function (array $acc, \stdClass $d) {
                $acc[] = (new DiscordRole((int) ($d->id)))->setName($d->name)->setPosition($d->position)->setColor($d->color);

                return $acc;
            }, []);
            usort($data, fn (DiscordRole $a, DiscordRole $b) => $a->getPosition() > $b->getPosition() ? 1 : ($a->getPosition() === $b->getPosition() ? 0 : -1));

            $i->set($data);
            $i->expiresAfter(self::EXPIRE_CURRENT_GUILD_ROLES);
            $this->cache->save($i);
        }

        return $i->get();
    }
}
