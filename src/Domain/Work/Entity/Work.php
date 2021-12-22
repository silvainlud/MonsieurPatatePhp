<?php

declare(strict_types=1);

namespace App\Domain\Work\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[Entity]
class Work
{
    #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(class: UuidGenerator::class)]
    #[Column(type: 'uuid', unique: true)]
    protected string $id;

    #[Column(type: 'string', length: 25)]
    protected string $name;

    #[Column(type: 'string')]
    protected string $description;

    #[Column(type: 'datetime')]
    protected string $creationDate;

    #[Column(type: 'datetime')]
    protected string $dueDate;

    #[Column(type: 'string', length: 25)]
    protected string $categoryId;

    #[Column(type: 'string', length: 25)]
    protected string $serverId;

    #[ManyToOne(targetEntity: WorkCategory::class, inversedBy: 'works')]
    private WorkCategory $work_category;
}
