<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity]
class WorkCategory
{
    #[Id, GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'string')]
    protected string $id;

    #[Column(type: 'string', length: 25)]
    protected string $name;

    #[OneToMany(mappedBy: 'work_category', targetEntity: Work::class)]
    protected Collection $works;
}
