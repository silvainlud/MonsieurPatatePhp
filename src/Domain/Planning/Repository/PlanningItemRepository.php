<?php

declare(strict_types=1);

namespace App\Domain\Planning\Repository;

use App\Domain\Planning\Entity\PlanningItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlanningItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanningItem::class);
    }

    /** @return PlanningItem[] */
    public function findByUuidNotIn(array $uuids): array
    {
        $qd = $this->createQueryBuilder('i');
        $qd->andWhere($qd->expr()->not($qd->expr()->in('i.id', ':uuids')))
            ->setParameter('uuids', $uuids);

        return $qd->getQuery()->getResult();
    }

    /** @return PlanningItem[] */
    public function findFuture(int $limit = 20): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.dateStart > :now')->setParameter('now', new \DateTime())
            ->addOrderBy('i.dateStart', 'ASC')
            ->addOrderBy('i.dateEnd', 'ASC')
            ->setMaxResults($limit)->getQuery()->getResult();
    }

    /** @return PlanningItem[] */
    public function findDate(?\DateTime $date = null): array
    {
        if ($date === null) {
            $date = new \DateTime();
        }

        return $this->findBetweenDates($date,$date->format("Y-m-d 23:59:59"));
    }

    /** @return PlanningItem[] */
    public function findBetweenDates(\DateTime|string $start,\DateTime|string $end): array
    {

        return $this->createQueryBuilder('i')
            ->andWhere('(i.dateStart BETWEEN :dateMin AND :dateMax) or (i.dateEnd BETWEEN :dateMin AND :dateMax)')->setParameters([
                'dateMin' => is_string($start) ? $start : $start->format("Y-m-d H:i:s"),
                'dateMax' => is_string($end) ? $end : $end->format('Y-m-d H:i:s'),
            ])
            ->addOrderBy('i.dateStart', 'ASC')
            ->addOrderBy('i.dateEnd', 'ASC')
            ->getQuery()->getResult();
    }
}
