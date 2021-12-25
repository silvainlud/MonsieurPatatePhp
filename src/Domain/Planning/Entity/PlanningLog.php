<?php

declare(strict_types=1);

namespace App\Domain\Planning\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[Entity]
class PlanningLog
{
    public const ACTION_TYPE_ADD = 'ADD';
    public const ACTION_TYPE_UPDATE = 'UPDATE';
    public const ACTION_TYPE_DELETE = 'DELETE';

    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_DATE_START = 'dateStart';
    public const FIELD_DATE_END = 'dateEnd';
    public const FIELD_TEACHER = 'teacher';
    public const FIELD_LOCATION = 'location';

    private const DATE_TIME_COMPARE = 'Y-m-d\\TH:i:s';
    private const DATE_TIME_NOTIFY = '2 weeks';

    #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(class: UuidGenerator::class)]
    #[Column(type: 'uuid', unique: true)]
    protected string $id;

    #[Column(type: 'string', length: 10)]
    protected string $actionType;

    #[Column(type: 'string', length: 64)]
    protected string $planningUuid;

    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $titlePrevious;

    #[Column(type: 'text', nullable: true)]
    protected ?string $descriptionPrevious;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $dateStartPrevious;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $dateEndPrevious;

    #[Column(type: 'string', nullable: true)]
    protected ?string $teacherPrevious;

    #[Column(type: 'string', nullable: true)]
    protected ?string $locationPrevious;

    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $titleNext;

    #[Column(type: 'text', nullable: true)]
    protected ?string $descriptionNext;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $dateStartNext;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $dateEndNext;

    #[Column(type: 'string', nullable: true)]
    protected ?string $teacherNext;

    #[Column(type: 'string', nullable: true)]
    protected ?string $locationNext;

    #[Column(type: 'datetime')]
    protected DateTime $dateCreate;

    #[Column(type: 'boolean')]
    protected bool $isDiscordSend;

    #[Column(type: 'json')]
    protected array $updatedField;

    public function __construct(?PlanningItem $prev = null, ?PlanningItem $next = null)
    {
        $this->dateCreate = new DateTime();
        $this->isDiscordSend = false;
        $this->updatedField = [];

        $this->titlePrevious = null;
        $this->descriptionPrevious = null;
        $this->dateStartPrevious = null;
        $this->dateEndPrevious = null;
        $this->teacherPrevious = null;
        $this->locationPrevious = null;
        $this->titleNext = null;
        $this->descriptionNext = null;
        $this->dateStartNext = null;
        $this->dateEndNext = null;
        $this->teacherNext = null;
        $this->locationNext = null;

        if ($prev === null && $next !== null) {
            $this->actionType = self::ACTION_TYPE_ADD;
            $this->planningUuid = $next->getId();
            $this->setNext($next);

            if (new DateTime(self::DATE_TIME_NOTIFY) < $next->getDateStart()) {
                $this->isDiscordSend = true;
            }
        } elseif ($prev !== null && $next !== null) {
            $this->actionType = self::ACTION_TYPE_UPDATE;
            $this->planningUuid = $next->getId();
            $this->setPrevious($prev);
            $this->setNext($next);

            if ($this->titlePrevious !== $this->titleNext) {
                $this->updatedField[] = self::FIELD_TITLE;
            }
            if ($this->descriptionPrevious !== $this->descriptionNext) {
                $this->updatedField[] = self::FIELD_DESCRIPTION;
            }
            if ($this->dateStartPrevious?->format(self::DATE_TIME_COMPARE) !== $this->dateStartNext?->format(self::DATE_TIME_COMPARE)) {
                $this->updatedField[] = self::FIELD_DATE_START;
            }
            if ($this->dateEndPrevious?->format(self::DATE_TIME_COMPARE) !== $this->dateEndNext?->format(self::DATE_TIME_COMPARE)) {
                $this->updatedField[] = self::FIELD_DATE_END;
            }
            if ($this->teacherPrevious !== $this->teacherNext) {
                $this->updatedField[] = self::FIELD_TEACHER;
            }
            if ($this->locationPrevious !== $this->locationNext) {
                $this->updatedField[] = self::FIELD_LOCATION;
            }

            if (new DateTime(self::DATE_TIME_NOTIFY) < $next->getDateStart() && new DateTime(self::DATE_TIME_NOTIFY) < $prev->getDateStart()) {
                $this->isDiscordSend = true;
            }
        } elseif ($prev !== null && $next === null) {
            $this->actionType = self::ACTION_TYPE_DELETE;
            $this->planningUuid = $prev->getId();
            $this->setPrevious($prev);

            if (new DateTime(self::DATE_TIME_NOTIFY) < $prev->getDateStart()) {
                $this->isDiscordSend = true;
            }
        }
    }

    public function setPrevious(PlanningItem $prev): void
    {
        $this->titlePrevious = $prev->getTitle();
        $this->descriptionPrevious = $prev->getDescription();
        $this->dateStartPrevious = $prev->getDateStart();
        $this->dateEndPrevious = $prev->getDateEnd();
        $this->teacherPrevious = $prev->getTeacher();
        $this->locationPrevious = $prev->getLocation();
    }

    public function setNext(PlanningItem $next): void
    {
        $this->titleNext = $next->getTitle();
        $this->descriptionNext = $next->getDescription();
        $this->dateStartNext = $next->getDateStart();
        $this->dateEndNext = $next->getDateEnd();
        $this->teacherNext = $next->getTeacher();
        $this->locationNext = $next->getLocation();
    }

    #[Pure]
    public static function isDiff(PlanningItem $prev, PlanningItem $next): bool
    {
        return $prev->getTitle() !== $next->getTitle()
            || $prev->getDescription() !== $next->getDescription()
            || $prev->getDateStart()->format(self::DATE_TIME_COMPARE) !== $next->getDateStart()->format(self::DATE_TIME_COMPARE)
            || $prev->getDateEnd()->format(self::DATE_TIME_COMPARE) !== $next->getDateEnd()->format(self::DATE_TIME_COMPARE)
            || $prev->getTeacher() !== $next->getTeacher()
            || $prev->getLocation() !== $next->getLocation();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function getDateCreate(): DateTime
    {
        return $this->dateCreate;
    }

    public function getDateEndNext(): ?DateTime
    {
        return $this->dateEndNext;
    }

    public function getDateEndPrevious(): ?DateTime
    {
        return $this->dateEndPrevious;
    }

    public function getDateStartNext(): ?DateTime
    {
        return $this->dateStartNext;
    }

    public function getDateStartPrevious(): ?DateTime
    {
        return $this->dateStartPrevious;
    }

    public function getDescriptionNext(): ?string
    {
        return $this->descriptionNext;
    }

    public function getDescriptionPrevious(): ?string
    {
        return $this->descriptionPrevious;
    }

    public function getLocationNext(): ?string
    {
        return $this->locationNext;
    }

    public function getLocationPrevious(): ?string
    {
        return $this->locationPrevious;
    }

    public function getPlanningUuid(): string
    {
        return $this->planningUuid;
    }

    public function getTeacherNext(): ?string
    {
        return $this->teacherNext;
    }

    public function getTeacherPrevious(): ?string
    {
        return $this->teacherPrevious;
    }

    public function getTitleNext(): ?string
    {
        return $this->titleNext;
    }

    public function getTitlePrevious(): ?string
    {
        return $this->titlePrevious;
    }

    public function getUpdatedField(): array
    {
        return $this->updatedField;
    }

    public function isDiscordSend(): bool
    {
        return $this->isDiscordSend;
    }

    public function setIsDiscordSend(bool $isDiscordSend): self
    {
        $this->isDiscordSend = $isDiscordSend;

        return $this;
    }
}
