<?php

declare(strict_types=1);

namespace App\Domain\Work\Entity;

use App\Domain\Guild\Entity\GuildSettings;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
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
    protected DateTime $creationDate;

    #[Column(type: 'datetime')]
    protected DateTime $dueDate;

    #[ManyToOne(targetEntity: GuildSettings::class)]
    #[JoinColumn(name: 'server_id', referencedColumnName: 'GuildId')]
    protected GuildSettings $guild;

    #[ManyToOne(targetEntity: WorkCategory::class, inversedBy: 'works')]
    #[JoinColumn(name: 'work_category_id')]
    private WorkCategory $work_category;

    public function __construct()
    {
        $this->creationDate = new DateTime();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getWorkCategory(): WorkCategory
    {
        return $this->work_category;
    }

    public function setWorkCategory(WorkCategory $work_category): self
    {
        $this->work_category = $work_category;

        return $this;
    }

    public function getGuild(): GuildSettings
    {
        return $this->guild;
    }

    public function setGuild(GuildSettings $guild): self
    {
        $this->guild = $guild;

        return $this;
    }
}
