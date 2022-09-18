<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel\Message\Embed;

class DiscordEmbed
{
    private string $title;
    private ?string $content = null;

    private ?DiscordEmbedAuthor $author = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?DiscordEmbedAuthor
    {
        return $this->author;
    }

    public function setAuthor(?DiscordEmbedAuthor $author): self
    {
        $this->author = $author;

        return $this;
    }
}
