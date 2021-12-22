<?php

declare(strict_types=1);

namespace App\Domain\Work\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
class WorkCategory
{
    #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(class: UuidGenerator::class)]
    #[Column(type: 'uuid')]
    protected string $id;

    #[Column(type: 'string', length: 25)]
    #[NotBlank, Length(max: 25)]
    protected string $name;

    #[Column(name: "active", type: "boolean", options: ["default" => true])]
    protected bool $active;

    #[OneToMany(mappedBy: 'work_category', targetEntity: Work::class)]
    protected Collection $works;

    public function __construct()
    {
        $this->active = true;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work))
            $this->works->add($work);

        return $this;
    }

    public function removeWork(Work $work): self
    {
        if ($this->works->contains($work))
            $this->works->removeElement($work);

        return $this;
    }

}
