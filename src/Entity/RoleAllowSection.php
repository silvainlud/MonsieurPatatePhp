<?php

declare(strict_types=1);

namespace App\Entity;

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

    #[Column(type: 'string', length: 25)]
    protected string $roleId;
}
