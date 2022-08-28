<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber;

use App\Domain\NotificationSubscriber\Entity\UserPushSubscriber;
use App\Domain\User\Entity\AbstractUser;

interface IUserPushSubscriberService
{
    public function register(AbstractUser $user, string $endpoint, string $p256dh, string $authKey): UserPushSubscriber;

    public function exist(string $endpoint): bool;

    public function send(UserPushSubscriber $subscriber, string $title, string $msg): void;

    public function sendAll(string $title, string $msg): void;

    public function getPublicKey(): string;
}
