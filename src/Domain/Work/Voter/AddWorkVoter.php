<?php

namespace App\Domain\Work\Voter;

use App\Domain\User\Entity\AbstractUser;
use App\Domain\User\Entity\DiscordUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AddWorkVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute == "ROLE_ADD_WORK" && ($subject === null || $subject instanceof AbstractUser);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var AbstractUser? $user */
        $user = ($subject instanceof AbstractUser) ? $subject : $token->getUser();
        return $user instanceof DiscordUser;
    }
}