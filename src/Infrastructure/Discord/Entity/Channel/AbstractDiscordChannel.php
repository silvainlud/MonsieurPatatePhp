<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel;

abstract class AbstractDiscordChannel
{
    public const TYPE_GUILD_TEXT = 0; // a text channel within a server
    public const TYPE_DM = 1; // a direct message between users
    public const TYPE_GUILD_VOICE = 2; //    a voice channel within a server
    public const TYPE_GROUP_DM = 3; //   a direct message between multiple users
    public const TYPE_GUILD_CATEGORY = 4; // an organizational category that contains up to 50 channels
    public const TYPE_GUILD_NEWS = 5; // a channel that users can follow and crosspost into their own server
    public const TYPE_GUILD_STORE = 6; //    a channel in which game developers can sell their game on Discord
    public const TYPE_GUILD_NEWS_THREAD = 10; // a temporary sub-channel within a GUILD_NEWS channel
    public const TYPE_GUILD_PUBLIC_THREAD = 11; //   a temporary sub-channel within a GUILD_TEXT channel
    public const TYPE_GUILD_PRIVATE_THREAD = 12; //  a temporary sub-channel within a GUILD_TEXT channel that is only viewable by those invited and those with the MANAGE_THREADS permission
    public const TYPE_GUILD_STAGE_VOICE = 13; // a voice channel for hosting events with an audience

    private int $id;
    private string $name;
    private int $position;

    public function __construct(int $id, string $name, ?int $position = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position ?? 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
