<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber\Entity;

use App\Domain\NotificationSubscriber\Repository\UserPushSubscriberRepository;
use App\Domain\User\Entity\AbstractUser;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Table]
#[Entity(repositoryClass: UserPushSubscriberRepository::class)]
class UserPushSubscriber
{
    #[Id, Column(type: 'string', length: 255)]
    protected string $endpoint;

    #[Column(type: 'string', length: 100)]
    protected string $keyP256dh;

    #[Column(type: 'string', length: 30)]
    protected string $keyAuth;

    #[ManyToOne(targetEntity: AbstractUser::class)]
    protected AbstractUser $user;

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getKeyP256dh(): string
    {
        return $this->keyP256dh;
    }

    public function setKeyP256dh(string $keyP256dh): self
    {
        $this->keyP256dh = $keyP256dh;

        return $this;
    }

    public function getKeyAuth(): string
    {
        return $this->keyAuth;
    }

    public function setKeyAuth(string $keyAuth): self
    {
        $this->keyAuth = $keyAuth;

        return $this;
    }

    public function getUser(): AbstractUser
    {
        return $this->user;
    }

    public function setUser(AbstractUser $user): self
    {
        $this->user = $user;

        return $this;
    }
}
