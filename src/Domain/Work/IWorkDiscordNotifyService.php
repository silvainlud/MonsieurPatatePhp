<?php

declare(strict_types=1);

namespace App\Domain\Work;

use App\Domain\Work\Entity\Work;

interface IWorkDiscordNotifyService
{
    public function notifyAdd(Work $work): void;
    public function notifyEdit(Work $work): void;
    public function notifyModify(Work $work): void;

    public function processRecall(): void;
}
