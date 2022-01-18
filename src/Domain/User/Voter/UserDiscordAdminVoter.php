<?php

declare(strict_types=1);

namespace App\Domain\User\Voter;

use App\Domain\User\Entity\AbstractUser;
use App\Domain\User\Entity\DiscordUser;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Discord\IDiscordUserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserDiscordAdminVoter extends Voter
{
    const DISCORD_ADMIN_ROLE_ADMIN = 'ROLE_ADMIN_DISCORD';

    public function __construct(private IDiscordUserService $discordUserService, private IDiscordGuildService $discordGuildService)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::DISCORD_ADMIN_ROLE_ADMIN && ($subject === null || $subject instanceof AbstractUser);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var AbstractUser $user */
        $user = ($subject instanceof AbstractUser) ? $subject : $token->getUser();

        if ($user instanceof DiscordUser) {
            $userRoles = $this->discordUserService->getRoles($user);
            if (\count($userRoles) === 0) {
                return false;
            }
            $guildRoles = $this->discordGuildService->getRoles();
            if (\count($guildRoles) === 0) {
                return false;
            }

            return $userRoles[array_key_first($userRoles)]->getId() === $guildRoles[array_key_first($guildRoles)]->getId()
                && ($userRoles[array_key_first($userRoles)]->getPermission() & 0x0000000008) === 0x0000000008;
        }
        return false;
    }
}
