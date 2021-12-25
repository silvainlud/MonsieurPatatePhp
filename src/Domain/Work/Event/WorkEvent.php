<?php

declare(strict_types=1);

namespace App\Domain\Work\Event;

use App\Domain\Work\Entity\Work;

class WorkEvent
{
    public const PRE_ADD = 'app.work.event.pre_add';
    public const POST_ADD = 'app.work.event.post_add';
    public const PRE_EDIT = 'app.work.event.pre_edit';
    public const POST_EDIT = 'app.work.event.post_edit';
    public const PRE_REMOVE = 'app.work.event.pre_remove';
    public const POST_REMOVE = 'app.work.event.post_remove';

    public function __construct(private Work $work)
    {
    }

    public function getWork(): Work
    {
        return $this->work;
    }
}
