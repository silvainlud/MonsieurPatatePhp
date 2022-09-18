<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel\Message\Embed;

class DiscordEmbedAuthor
{
    private string $name;
    private ?string $url = null;
    private ?string $icon_url = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->icon_url;
    }

    public function setIconUrl(?string $icon_url): self
    {
        $this->icon_url = $icon_url;

        return $this;
    }
}
