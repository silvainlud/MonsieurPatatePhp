<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\UserSecretGenerator;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class DiscordUser extends AbstractUser
{
    #[Column(type: 'string', length: 32, nullable: false)]
    private string $avatar;

    #[Column(type: 'string', length: 25)]
    private string $discordId;

    #[Column(type: 'string', length: 32, nullable: false)]
    private string $secretKey;

    public function __construct()
    {
        parent::__construct();
        $this->secretKey = UserSecretGenerator::generateSecret();
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
