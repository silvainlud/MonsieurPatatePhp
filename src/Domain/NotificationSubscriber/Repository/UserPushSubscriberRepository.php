<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber\Repository;

use App\Domain\NotificationSubscriber\Entity\UserPushSubscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserPushSubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPushSubscriber::class);
    }

    public function exist(string $endpoint): bool
    {
        return $this->createQueryBuilder('e')
            ->where('e.endpoint = :endpoint')->setParameter('endpoint', $endpoint)
            ->setMaxResults(1)->select('1')
            ->getQuery()->getOneOrNullResult() !== null;
    }
}
