<?php

declare(strict_types=1);

namespace App\Domain\Planning;

interface IPlanningSynchronizeService
{
    public function reload(): array;
}
