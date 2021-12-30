<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\UserSecretGenerator;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Id, GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 25)]
    private string $username;

    #[Column(type: 'string', length: 25)]
    private string $email;

    #[Column(type: 'string', length: 32, nullable: false)]
    private string $avatar;

    #[Column(type: 'string', length: 25)]
    private string $discordId;

    #[Column(type: 'string', length: 32, nullable: false)]
    private string $secretKey;


    public function __construct()
    {
        $this->secretKey = UserSecretGenerator::generateSecret();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDiscordId(): string
    {
        return $this->discordId;
    }

    public function setDiscordId(string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->secretKey;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }
}
