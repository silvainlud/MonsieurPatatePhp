<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
class Section
{
    public const visibility_show = "SHOW";
    public const visibility_hide = "HIDE";
    public const visibility_restrict = "RESTRICT";
    public const visibilities = [self::visibility_show, self::visibility_hide, self::visibility_restrict];

    #[Id, GeneratedValue(strategy: "AUTO")]
    #[Column(type: "string")]
    protected string $id;

    #[Column(type: "string", length: 25)]
    protected string $serverId;

    #[Column(type: "string", length: 25)]
    protected string $categoryId;

    #[Column(type: "string", length: 25, nullable: true)]
    protected ?string $creatorId;

    #[Column(type: "string", length: 25)]
    protected string $creatorName;

    #[Column(type: "datetime")]
    protected DateTime $date;

    #[Column(type: "string", length: 25)]
    protected string $name;

    #[Column(type: "string", length: 5)]
    protected string $emoji;

    #[Column(type: "string", length: 10)]
    protected string $visibility;

    #[Column(type: "string", length: 25)]
    protected string $roleId;

    #[Column(type: "string", length: 25, nullable: true)]
    protected ?string $announceChannelId;

    #[OneToMany(mappedBy: "section", targetEntity: RoleAllowSection::class)]
    protected Collection $allowRoles;

    public function __construct()
    {
    }
}
