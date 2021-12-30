<?php

declare(strict_types=1);

namespace App\Domain\Planning\Event;

use App\Domain\Planning\Entity\PlanningScreen;

class PlanningScreenEvent
{
    public const PLANNING_SCREEN_UPDATE = 'app.planning_screen.event.pre_update';

    public function __construct(private PlanningScreen $screen)
    {
    }

    public function getScreen(): PlanningScreen
    {
        return $this->screen;
    }
}
