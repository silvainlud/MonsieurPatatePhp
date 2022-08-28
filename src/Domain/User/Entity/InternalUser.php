<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class InternalUser extends AbstractUser
{
    #[Column(type: 'string', length: 62)]
    private string $password;

    #[Column(type: 'json')]
    private array $roles;

    private ?string $plainPassword;

    public function __construct()
    {
        parent::__construct();
        $this->plainPassword = null;
        $this->roles = [];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getRoles(): array
    {
        return array_unique(array_merge($this->roles, parent::getRoles()));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
