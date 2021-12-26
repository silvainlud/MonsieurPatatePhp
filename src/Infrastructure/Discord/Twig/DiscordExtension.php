<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Twig;

use App\Domain\User\Entity\User;
use App\Infrastructure\Discord\DiscordUserService;
use App\Infrastructure\Discord\Entity\DiscordGuild;
use App\Infrastructure\Discord\Entity\DiscordRole;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Parameter\IParameterService;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DiscordExtension extends AbstractExtension
{
    public function __construct(
        private DiscordUserService $userService,
        private IDiscordGuildService $guildService,
        private IParameterService $parameterService,
        private Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_discord_avatar', [$this, 'getAvatarUser']),
            new TwigFunction('user_discord_username', [$this, 'getDiscordUsername']),
            new TwigFunction('current_guild', [$this, 'getCurrentGuild']),
            new TwigFunction('current_guild_icon', [$this, 'getCurrentGuildIcon']),
            new TwigFunction('current_user_highest_role', [$this, 'getHighestRole']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('discord_color', [$this, 'toDiscordColor']),
        ];
    }

    public function getDiscordUsername(): string
    {
        $u = $this->security->getUser();
        if (!$u instanceof User) {
            return '???';
        }
        $discordUser = $this->guildService->getGuildMember($this->parameterService->getGuildId(), $u->getDiscordId());
        if ($discordUser === null) {
            return $u->getUsername();
        }

        return $discordUser->getCompleteName();
    }

    public function getAvatarUser(User $user): string
    {
        return $this->userService->getAvatarUser($user);
    }

    public function getCurrentGuild(): DiscordGuild
    {
        return $this->guildService->getCurrentGuild();
    }

    public function getCurrentGuildIcon(): string
    {
        return $this->guildService->getCurrentGuildIcon();
    }

    public function getHighestRole(User $user): ?DiscordRole
    {
        $roles = $this->userService->getRoles($user);
        if (\count($roles) === 0) {
            return null;
        }

        return $roles[array_key_first($roles)];
    }

    public function toDiscordColor(int $color): string
    {
        return '#' . dechex($color);
    }
}
