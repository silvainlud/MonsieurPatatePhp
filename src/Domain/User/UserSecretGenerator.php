<?php

declare(strict_types=1);

namespace App\Domain\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class UserSecretGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity): string
    {
        return self::generateSecret();
    }

    public static function generateSecret(): string
    {
        return md5(uniqid());
    }
}
