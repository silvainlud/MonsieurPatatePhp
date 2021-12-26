<?php

namespace App\Infrastructure\Discord\Entity;

class DiscordUser
{
    protected string $id;
    protected string $username;
    protected string $avatar;
    protected string $discriminator;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    public function setDiscriminator(string $discriminator): self
    {
        $this->discriminator = $discriminator;

        return $this;
    }

}