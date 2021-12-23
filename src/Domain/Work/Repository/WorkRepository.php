<?php

namespace App\Domain\Work\Repository;

use App\Domain\Work\Entity\Work;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Work::class);
    }

    /** @return Work[] */
    public function findCurrentWork(): array
    {
        return $this->createQueryBuilder("w")
            ->join("w.work_category", "cat")->addSelect("cat")
            ->andWhere("w.dueDate >= :now")
            ->andWhere("cat.active = :active")
            ->setParameter("active", true)
            ->orderBy("w.dueDate", "ASC")
            ->addOrderBy("w.name", "ASC")
            ->setParameter("now", new \DateTime())->getQuery()->getResult();
    }
}