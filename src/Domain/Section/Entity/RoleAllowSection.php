<?php

declare(strict_types=1);

namespace App\Domain\Section\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class RoleAllowSection
{
    #[Id]
    #[ManyToOne(targetEntity: Section::class, inversedBy: 'allowRoles'), JoinColumn(name: 'SectionId')]
    protected Section $section;

    #[Id]
    #[Column(type: 'string', length: 25)]
    protected string $roleId;

    public function getSection(): Section
    {
        return $this->section;
    }

    public function setSection(Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getRoleId(): string
    {
        return $this->roleId;
    }

    public function setRoleId(string $roleId): self
    {
        $this->roleId = $roleId;

        return $this;
    }
}
