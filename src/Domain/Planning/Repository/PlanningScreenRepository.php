<?php

namespace App\Domain\Planning\Repository;

use App\Domain\Planning\Entity\PlanningScreen;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanningScreen|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanningScreen|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanningScreen[]    findAll()
 * @method PlanningScreen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningScreenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PlanningItemRepository $itemRepository)
    {
        parent::__construct($registry, PlanningScreen::class);
    }

    public function getCurrent(): ?PlanningScreen
    {

        $current = new \DateTime();
        $week = (int)$current->format('W');
        $year = (int)$current->format('o');
        [, $end] = $this::getStartAndEndDate($week, $year);
        $items = $this->itemRepository->findBetweenDates(
            $current,
            $end->format('Y-m-d 23:59:59')
        );

        if (\count($items) === 0) {
            $current = $current->modify('+ 7 days');
            $week = (int)$current->format('W');
            $year = (int)$current->format('o');
        }

        $screen = $this->findOneBy([
            'year' => 2022,
            'week' => 20,
        ]);

        if ($screen instanceof PlanningScreen)
            return $screen;
        return null;
    }

    /** @return DateTime[] */
    public static function getStartAndEndDate(int $week, int $year): array
    {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret[0] = clone $dto;
        $dto->modify('+6 days');
        $ret[1] = $dto;

        return $ret;
    }
}