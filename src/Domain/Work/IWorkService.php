<?php

declare(strict_types=1);

namespace App\Domain\Work;

use App\Domain\Work\Entity\Work;

interface IWorkService
{
    public const RECALL_PERIOD = ['- 2 weeks', '- 1 weeks', '- 3 days', '- 17 hours'];

    public function calculateNextRecallDate(Work $work): Work;
}
