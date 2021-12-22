<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel;

interface ICategoryChannelParent
{
    public function getParent(): ?CategoryChannel;

    public function setParent(?CategoryChannel $parent): self;
}
