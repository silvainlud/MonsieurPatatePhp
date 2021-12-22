<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel;

class VoiceChannel extends AbstractDiscordChannel implements ICategoryChannelParent
{
    private ?CategoryChannel $parent;

    public function getParent(): ?CategoryChannel
    {
        return $this->parent;
    }

    public function setParent(?CategoryChannel $parent): self
    {
        if (isset($this->parent) && $parent !== null && $this->parent !== $parent) {
            $this->parent->removeChannels($this);
        }

        $this->parent = $parent;

        if ($this->parent !== null) {
            $this->parent->addChannels($this);
        }

        return $this;
    }
}
