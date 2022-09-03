<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber\Data;

use App\Domain\User\Entity\AbstractUser;

class SendNotificationData
{
    private ?AbstractUser $user = null;
    private string $title;
    private string $message;

    private bool $sendAll = false;

    public function getUser(): ?AbstractUser
    {
        return $this->user;
    }

    public function setUser(?AbstractUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function isSendAll(): bool
    {
        return $this->sendAll;
    }

    public function setSendAll(bool $sendAll): self
    {
        $this->sendAll = $sendAll;

        return $this;
    }
}
