<?php

declare(strict_types=1);

namespace App\Domain\Planning;

interface IPlanningNotifyProcessService
{
    public function notifyLogs(): void;
}
