<?php

namespace App\Domain\NotificationSubscriber\Data;

use App\Domain\User\Entity\AbstractUser;

class SendNotificationData
{
    private AbstractUser $user;
    private string $title;
    private string $message;

    public function getUser(): AbstractUser
    {
        return $this->user;
    }

    public function setUser(AbstractUser $user): self
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


}