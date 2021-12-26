<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Infrastructure\Discord\Entity\Channel\AbstractDiscordChannel;
use App\Infrastructure\Discord\Entity\Channel\CategoryChannel;
use App\Infrastructure\Discord\Entity\Channel\ICategoryChannelParent;
use App\Infrastructure\Discord\Entity\Channel\TextChannel;
use App\Infrastructure\Discord\Entity\Channel\VoiceChannel;
use App\Infrastructure\Discord\Entity\DiscordGuild;
use App\Infrastructure\Discord\Entity\DiscordMember;
use App\Infrastructure\Discord\Entity\DiscordRole;
use App\Infrastructure\Discord\Entity\DiscordUser;
use App\Infrastructure\Parameter\IParameterService;
use Psr\Cache\CacheItemPoolInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordGuildService implements IDiscordGuildService
{
    private const CACHE_KEY_GUILD_MEMBER = 'guild_member';
    private const EXPIRE_GUILD_MEMBER = 3600;

    private const CACHE_KEY_GUILD_MEMBERS = 'guild_members_list_';
    private const EXPIRE_GUILD_MEMBERS = 300;

    private const CACHE_KEY_CURRENT_GUILD = 'current_guild';
    private const EXPIRE_CURRENT_GUILD = 259200;

    private const CACHE_KEY_CURRENT_GUILD_ROLES = 'current_guild_roles';
    private const EXPIRE_CURRENT_GUILD_ROLES = 259200;

    private const CACHE_KEY_GUILD_CHANNELS = 'guild_channels_';
    private const EXPIRE_GUILD_CHANNELS = 300;

    public function __construct(
        private HttpClientInterface    $discordClient,
        private CacheItemPoolInterface $cache,
        private IParameterService      $parameterService
    )
    {
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
            $guild = (new DiscordGuild((int)($data->id)))->setName($data->name)->setIcon($data->icon);
            $i->set($guild);
            $i->expiresAfter(self::EXPIRE_CURRENT_GUILD);
            $this->cache->save($i);
        }

        return $i->get();
    }

    /** @inheritDoc */
    public function getGuildMembers(string $guildId): array
    {
        $i = $this->cache->getItem(self::CACHE_KEY_GUILD_MEMBERS . $guildId);
        if (!$i->isHit()) {
            try {
                $response = $this->discordClient->request(Request::METHOD_GET, 'guilds/' . $guildId . "/members", [
                    "query" => ["limit" => 1000]
                ]);
            } catch (TransportExceptionInterface $e) {
                return [];
            }
            $data = array_reduce(json_decode($response->getContent(false)), function (array $acc, stdClass $d) {
                $u = (new DiscordUser((string)$d->user->id))
                    ->setUsername($d->user->username)
                    ->setAvatar($d->user->avatar)
                    ->setDiscriminator($d->user->discriminator);
                $acc[] = (new DiscordMember($u))->setNick($d->nick);

                return $acc;
            }, []);
            $i->set($data);
            $i->expiresAfter(self::EXPIRE_GUILD_MEMBERS);
            $this->cache->save($i);
        }

        return $i->get();
    }

    public function getGuildMember(string $guildId, string $memberId): ?DiscordMember
    {
        $i = $this->cache->getItem(self::CACHE_KEY_GUILD_MEMBER . $guildId . "_" . $memberId);

        if (!$i->isHit()) {
            try {
                $response = $this->discordClient->request(Request::METHOD_GET, 'guilds/' . $guildId . "/members/" . $memberId);
            } catch (TransportExceptionInterface $e) {
                return null;
            }
            if ($response->getStatusCode() === Response::HTTP_OK) {
                $d = json_decode($response->getContent(false));
                $u = (new DiscordUser((string)$d->user->id))
                    ->setUsername($d->user->username)
                    ->setAvatar($d->user->avatar)
                    ->setDiscriminator($d->user->discriminator);
                $data = (new DiscordMember($u))->setNick($d->nick);
            } else
                $data = null;

            $i->set($data);
            $i->expiresAfter(self::EXPIRE_GUILD_MEMBER);
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
            $data = array_reduce(json_decode($response->getContent(false)), function (array $acc, stdClass $d) {
                $acc[] = (new DiscordRole((int)($d->id)))
                    ->setName($d->name)
                    ->setPosition($d->position)
                    ->setColor($d->color)
                    ->setPermission($d->permissions);

                return $acc;
            }, []);
            usort($data, fn(DiscordRole $a, DiscordRole $b) => $a->getPosition() > $b->getPosition() ? -1 : ($a->getPosition() === $b->getPosition() ? 0 : 1));

            $i->set($data);
            $i->expiresAfter(self::EXPIRE_CURRENT_GUILD_ROLES);
            $this->cache->save($i);
        }

        return $i->get();
    }

    /** {@inheritDoc} */
    public function getChannels(int $guildId): array
    {
        $i = $this->cache->getItem(self::CACHE_KEY_GUILD_CHANNELS . $guildId);
        if (!$i->isHit()) {
            try {
                $response = $this->discordClient->request(Request::METHOD_GET, 'guilds/' . $this->parameterService->getGuildId() . '/channels');
            } catch (TransportExceptionInterface $e) {
                return [];
            }

            $channels = json_decode($response->getContent(false));

            $data = array_reduce($channels, function (array $acc, stdClass $d) {
                if ($d->type === AbstractDiscordChannel::TYPE_GUILD_CATEGORY) {
                    $acc[] = $this->_getChannel($d);
                }

                return $acc;
            }, []);

            $guildChannels = array_filter($channels, fn(stdClass $obj) => \in_array($obj->type, [AbstractDiscordChannel::TYPE_GUILD_TEXT, AbstractDiscordChannel::TYPE_GUILD_VOICE], true));
            foreach ($guildChannels as $c) {
                $nc = $this->_getChannel($c);
                if ($nc instanceof ICategoryChannelParent && $c->parent_id !== null) {
                    $ps = array_filter($data, fn(AbstractDiscordChannel $o) => $o->getId() === (int)$c->parent_id);
                    if (\count($ps) !== 0) {
                        $nc->setParent($ps[array_key_first($ps)]);
                    }
                } else {
                    $data[] = $nc;
                }
            }

            $i->set($data);
            $i->expiresAfter(self::EXPIRE_GUILD_CHANNELS);
            $this->cache->save($i);
        }

        return $i->get();
    }

    private function _getChannel(stdClass $obj): ?AbstractDiscordChannel
    {
        return match ($obj->type) {
            AbstractDiscordChannel::TYPE_GUILD_CATEGORY => (new CategoryChannel((int)$obj->id, $obj->name, $obj->position)),
            AbstractDiscordChannel::TYPE_GUILD_VOICE => (new VoiceChannel((int)$obj->id, $obj->name, $obj->position)),
            AbstractDiscordChannel::TYPE_GUILD_TEXT => (new TextChannel((int)$obj->id, $obj->name, $obj->position)),
            default => null,
        };
    }
}
