<?php

declare(strict_types=1);

namespace App\Domain\Work\Entity;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Work\Repository\WorkRepository;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[Entity(repositoryClass: WorkRepository::class)]
class Work
{
    #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(class: UuidGenerator::class)]
    #[Column(type: 'uuid', unique: true)]
    protected string $id;

    #[Column(type: 'string', length: 50)]
    #[NotBlank, Length(max: 50)]
    protected string $name;

    #[Column(type: 'text')]
    #[NotBlank]
    protected string $description;

    #[Column(type: 'datetime')]
    protected DateTime $creationDate;

    #[Column(type: 'datetime')]
    #[NotNull, GreaterThanOrEqual("now")]
    protected DateTime $dueDate;

    #[ManyToOne(targetEntity: GuildSettings::class)]
    #[JoinColumn(name: 'server_id', referencedColumnName: 'GuildId')]
    protected GuildSettings $guild;

    #[Column(type: 'string', length: 40, nullable: true)]
    protected ?string $messageId;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $recallDate;

    #[ManyToOne(targetEntity: WorkCategory::class, inversedBy: 'works')]
    #[JoinColumn(name: 'work_category_id')]
    private WorkCategory $work_category;

    public function __construct()
    {
        $this->creationDate = new DateTime();
        $this->recallDate = null;
        $this->messageId = null;
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

    public function setDueDate(?DateTime $dueDate): self
    {
        if ($dueDate !== null)
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

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageId(?string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getRecallDate(): ?DateTime
    {
        return $this->recallDate;
    }

    public function setRecallDate(?DateTime $recallDate): self
    {
        $this->recallDate = $recallDate;

        return $this;
    }
}
