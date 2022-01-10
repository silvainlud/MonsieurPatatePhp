<?php

declare(strict_types=1);

namespace App\Domain\Planning\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class PlanningScreen
{
    #[Id, Column(type: 'integer')]
    protected int $week;
    #[Id, Column(type: 'integer')]
    protected int $year;

    #[Column(type: 'blob', nullable: false)]
    /** @var resource */
    protected mixed $file;

    #[Column(type: 'datetime')]
    protected \DateTime $createdDate;

    #[Column(type: 'datetime')]
    protected \DateTime $modifiedDate;

    public function __construct(?int $week = null, ?int $year = null)
    {
        if ($week !== null) {
            $this->week = $week;
        }
        if ($year !== null) {
            $this->year = $year;
        }
        $this->createdDate = new \DateTime();
        $this->modifiedDate = $this->createdDate;
    }

    public function getWeek(): int
    {
        return $this->week;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    /** @return resource */
    public function getFile(): mixed
    {
        return $this->file;
    }

    public function setFile(mixed $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getCreatedDate(): \DateTime
    {
        return $this->createdDate;
    }

    public function getModifiedDate(): \DateTime
    {
        return $this->modifiedDate;
    }

    public function setModifiedDate(\DateTime $modifiedDate): self
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }
}
