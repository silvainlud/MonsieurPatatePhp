<?php

declare(strict_types=1);

namespace App\Domain\Section\Entity;

use App\Domain\Guild\Entity\GuildSettings;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
// #[UniqueConstraint(name: "emoji_guild_unique", columns: ["server_id", "emoji"])]
#[UniqueConstraint(name: 'name_guild_unique', columns: ['server_id', 'name'])]
#[UniqueConstraint(name: 'category_guild_unique', columns: ['server_id', 'category_id'])]
class Section
{
    public const visibility_show = 'SHOW';
    public const visibility_hide = 'HIDE';
    public const visibility_restrict = 'RESTRICT';
    public const visibilities = [self::visibility_show, self::visibility_hide, self::visibility_restrict];

    #[Id]
    #[Column(type: 'string')]
    protected string $id;

    #[Column(type: 'string', length: 25)]
    protected string $categoryId;

    #[Column(type: 'string', length: 25, nullable: true)]
    protected ?string $creatorId;

    #[Column(type: 'string', length: 25)]
    protected string $creatorName;

    #[Column(type: 'datetime')]
    protected DateTime $date;

    #[Column(type: 'string', length: 25)]
    protected string $name;

    #[Column(type: 'blob')]
    /** @var resource */
    protected mixed $emoji;

    #[Column(type: 'string', length: 10)]
    protected string $visibility;

    #[Column(type: 'string', length: 25, unique: true)]
    protected string $roleId;

    #[Column(type: 'string', length: 25, nullable: true)]
    protected ?string $announceChannelId;

    #[OneToMany(mappedBy: 'section', targetEntity: RoleAllowSection::class, cascade: ['persist'])]
    protected Collection $allowRoles;

    #[ManyToOne(targetEntity: GuildSettings::class, inversedBy: 'sections')]
    #[JoinColumn(name: 'server_id', referencedColumnName: 'GuildId')]
    private GuildSettings $guildSettings;

    public function __construct()
    {
        $this->id = Uuid::v6()->toRfc4122();
        $this->allowRoles = new ArrayCollection();
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

    public function getAnnounceChannelId(): ?string
    {
        return $this->announceChannelId;
    }

    public function setAnnounceChannelId(?string $announceChannelId): self
    {
        $this->announceChannelId = $announceChannelId;

        return $this;
    }

    public function getAllowRoles(): Collection
    {
        return $this->allowRoles;
    }

    public function addAllowRole(RoleAllowSection $section): self
    {
        if (!$this->allowRoles->contains($section)) {
            $this->allowRoles->add($section);
        }

        return $this;
    }

    public function removeAllowRole(RoleAllowSection $section): self
    {
        if ($this->allowRoles->contains($section)) {
            $this->allowRoles->removeElement($section);
        }

        return $this;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function setCategoryId(string $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /** @return resource */
    public function getEmoji(): mixed
    {
        return $this->emoji;
    }

    /** @param resource $emoji */
    public function setEmoji(mixed $emoji): self
    {
        $this->emoji = $emoji;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

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

    public function getCreatorId(): ?string
    {
        return $this->creatorId;
    }

    public function setCreatorId(?string $creatorId): self
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    public function getCreatorName(): string
    {
        return $this->creatorName;
    }

    public function setCreatorName(string $creatorName): self
    {
        $this->creatorName = $creatorName;

        return $this;
    }

    public function getGuildSettings(): GuildSettings
    {
        return $this->guildSettings;
    }

    public function setGuildSettings(GuildSettings $guildSettings): self
    {
        $this->guildSettings = $guildSettings;

        return $this;
    }

    public function getEmojiString(): string
    {
        return (string) stream_get_contents($this->emoji);
    }
}
