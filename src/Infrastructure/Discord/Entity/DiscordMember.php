<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity;

use JetBrains\PhpStorm\Pure;

class DiscordMember
{
    private DiscordUser $user;
    private ?string $nick;

    public function __construct(DiscordUser $user)
    {
        $this->nick = null;
        $this->user = $user;
    }

    #[Pure]
    public function getCompleteName(): string
    {
        return $this->nick ?? $this->user->getUsername();
    }

    public function getUser(): DiscordUser
    {
        return $this->user;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }
}
