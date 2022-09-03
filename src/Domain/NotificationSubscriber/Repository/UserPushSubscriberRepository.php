<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber\Repository;

use App\Domain\NotificationSubscriber\Entity\UserPushSubscriber;
use App\Domain\User\Entity\AbstractUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    /** @return AbstractUser[] */
    public function getRegisteredUsers(): array
    {
        return $this->getEntityManager()->getRepository(AbstractUser::class)
            ->createQueryBuilder('user')
            ->join(UserPushSubscriber::class, 'push', Join::WITH, 'push.user = user')
            ->select('user')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}
