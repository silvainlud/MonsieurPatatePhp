<?php

declare(strict_types=1);

namespace App\Infrastructure\Parameter;

interface IParameterService
{
    public function getGuildId(): string;

    public function getPlanningWebSite(): string;
}
