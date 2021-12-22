<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

class CategoryChannel extends AbstractDiscordChannel
{
    /** @var Collection<AbstractDiscordChannel> */
    private Collection $channels;

    #[Pure]
    public function __construct(int $id, string $name, ?int $position = null)
    {
        parent::__construct($id, $name, $position);
        $this->channels = new ArrayCollection();
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannels(AbstractDiscordChannel $channel): Collection
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
            if ($channel instanceof ICategoryChannelParent && $channel->getParent() === null) {
                $channel->setParent($this);
            }
        }

        return $this->channels;
    }

    public function removeChannels(AbstractDiscordChannel $channel): Collection
    {
        if ($this->channels->contains($channel)) {
            $this->channels->removeElement($channel);
            if ($channel instanceof ICategoryChannelParent && $channel->getParent() === $this) {
                $channel->setParent(null);
            }
        }

        return $this->channels;
    }
}
