<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class Work
{
    #[Id, GeneratedValue(strategy: "AUTO")]
    #[Column(type: "string")]
    protected string $id;

    #[Column(type: "string", length: 25)]
    protected string $name;

    #[Column(type: "string")]
    protected string $description;

    #[Column(type: "datetime")]
    protected string $creationDate;

    #[Column(type: "datetime")]
    protected string $dueDate;

    #[Column(type: "string", length: 25)]
    protected string $categoryId;

    #[Column(type: "string", length: 25)]
    protected string $serverId;

    #[ManyToOne(targetEntity: WorkCategory::class, inversedBy: "works")]
    private WorkCategory $work_category;
}
