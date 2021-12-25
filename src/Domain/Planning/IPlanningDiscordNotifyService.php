<?php

namespace App\Domain\Planning;

use App\Domain\Planning\Entity\PlanningLog;

interface IPlanningDiscordNotifyService
{
    public function notifyLogs() :void;
}