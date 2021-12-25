<?php

declare(strict_types=1);

namespace App\Domain\Planning\Entity;

use App\Domain\Planning\Repository\PlanningItemRepository;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: PlanningItemRepository::class)]
class PlanningItem
{
    #[Id, Column(type: 'string', length: 64)]
    protected string $id;

    #[Column(type: 'string', length: 255)]
    protected string $title;

    #[Column(type: 'text')]
    protected string $description;

    #[Column(type: 'datetime')]
    protected DateTime $dateStart;

    #[Column(type: 'datetime')]
    protected DateTime $dateEnd;

    #[Column(type: 'datetime')]
    protected DateTime $dateCreated;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $dateModified;

    #[Column(type: 'string', nullable: true)]
    protected ?string $teacher;

    #[Column(type: 'string', nullable: true)]
    protected ?string $location;

    public function __construct(string $id, DateTime $dateCreated)
    {
        $this->id = $id;
        $this->dateCreated = $dateCreated;
        $this->dateModified = null;
        $this->teacher = null;
        $this->location = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDateStart(): DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(DateTime $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(DateTime $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateModified(?DateTime $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    public function getDateModified(): ?DateTime
    {
        return $this->dateModified;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function update(self $i): void
    {
        $this->setTitle($i->getTitle());
        $this->setDescription($i->getDescription());
        $this->setLocation($i->getLocation());
        $this->setTeacher($i->getTeacher());
        $this->setDateStart($i->getDateStart());
        $this->setDateEnd($i->getDateEnd());
        $this->setDateModified($i->getDateModified());
    }
}
