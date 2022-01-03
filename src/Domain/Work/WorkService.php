<?php

declare(strict_types=1);

namespace App\Domain\Work;

use App\Domain\Work\Entity\Work;
use App\Domain\Work\Repository\WorkRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class WorkService implements IWorkService
{
    public function __construct(private WorkRepository $workRepository, private EntityManagerInterface $em)
    {
    }

    public function calculateNextRecallDate(Work $work): Work
    {
        $now = new DateTime();
        if ($work->getDueDate() > $now) {
            $possibilityDates = array_map(fn (string $v) => (clone $work->getDueDate())->modify($v), self::RECALL_PERIOD);
            $possibilityDates = array_filter($possibilityDates, fn (DateTime $d) => $d > $now);
            if (\count($possibilityDates) > 0) {
                sort($possibilityDates);
                $work->setRecallDate($possibilityDates[array_key_first($possibilityDates)]);
            } else {
                $work->setRecallDate(null);
            }
        } else {
            $work->setRecallDate(null);
        }

        return $work;
    }

    public function resetAllRecallDate(): void
    {
        $works = $this->workRepository->findCurrentWork();
        foreach ($works as $w) {
            $this->calculateNextRecallDate($w);
        }
        $this->em->flush();
    }
}
