<?php

declare(strict_types=1);

namespace App\Domain\Guild\Entity;

use App\Domain\Section\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use JetBrains\PhpStorm\Pure;

#[Entity]
class GuildSettings
{
    #[Id, Column(name: 'GuildId', type: 'string', length: 25)]
    private string $serverId;

    #[Column(type: 'string', length: 25, nullable: true)]
    private ?string $announceChannelId;

    #[Column(type: 'string', length: 25, nullable: true)]
    private ?string $sectionMessageId;

    #[Column(type: 'string', length: 25, nullable: true)]
    private ?string $workAnnounceChannelId;

    #[Column(type: 'string', length: 25, nullable: true)]
    private ?string $planningNotifyChannelId;

    #[Column(type: 'string', length: 25, nullable: true)]
    private ?string $workRecallChannelId;

    /** @var Collection<Section> */
    #[OneToMany(mappedBy: 'guildSettings', targetEntity: Section::class, cascade: ['persist'])]
    private Collection $sections;

    #[Pure]
    public function __construct(string $serverId)
    {
        $this->serverId = $serverId;
        $this->announceChannelId = null;
        $this->sectionMessageId = null;
        $this->workAnnounceChannelId = null;
        $this->workRecallChannelId = null;
        $this->planningNotifyChannelId = null;
        $this->sections = new ArrayCollection();
    }

    public function getServerId(): string
    {
        return $this->serverId;
    }

    public function getAnnounceChannelId(): ?string
    {
        return $this->announceChannelId;
    }

    public function setAnnounceChannelId(?string $announceChannelId): self
    {
        $this->announceChannelId = $announceChannelId;

        return $this;
    }

    public function getSectionMessageId(): ?string
    {
        return $this->sectionMessageId;
    }

    public function setSectionMessageId(?string $sectionMessageId): self
    {
        $this->sectionMessageId = $sectionMessageId;

        return $this;
    }

    public function getWorkAnnounceChannelId(): ?string
    {
        return $this->workAnnounceChannelId;
    }

    public function setWorkAnnounceChannelId(?string $workAnnounceChannelId): self
    {
        $this->workAnnounceChannelId = $workAnnounceChannelId;

        return $this;
    }

    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
        }

        return $this;
    }

    public function getWorkRecallChannelId(): ?string
    {
        return $this->workRecallChannelId;
    }

    public function setWorkRecallChannelId(?string $workRecallChannelId): self
    {
        $this->workRecallChannelId = $workRecallChannelId;

        return $this;
    }

    public function getPlanningNotifyChannelId(): ?string
    {
        return $this->planningNotifyChannelId;
    }

    public function setPlanningNotifyChannelId(?string $planningNotifyChannelId): self
    {
        $this->planningNotifyChannelId = $planningNotifyChannelId;

        return $this;
    }
}
