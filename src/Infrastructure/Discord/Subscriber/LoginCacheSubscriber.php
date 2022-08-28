<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Subscriber;

use App\Domain\User\Entity\DiscordUser;
use App\Infrastructure\Discord\DiscordUserService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginCacheSubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [LoginSuccessEvent::class => 'onLoginSuccess'];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $u = $event->getUser();
        if ($u instanceof DiscordUser) {
            $this->cache->deleteItem(DiscordUserService::CACHE_KEY_USER_ROLES . $u->getDiscordId());
        }
    }
}
