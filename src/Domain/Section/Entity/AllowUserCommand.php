<?php

declare(strict_types=1);

namespace App\Domain\Section\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class AllowUserCommand
{
    #[Id, Column(type: 'string', length: 25)]
    private string $memberId;
}
