<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Enum;

enum WorkNotificationEnum
{
    case ADD;

    case EDIT;

    case REMOVE;

    case RECALL;
}
