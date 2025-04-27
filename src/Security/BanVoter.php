<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;

class BanVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // This voter applies to any authenticated request
        return in_array($attribute, ['ROLE_CLIENT', 'ROLE_MECANICIEN', 'ROLE_ADMIN', 'IS_AUTHENTICATED_FULLY']);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If no user or not a User entity, allow access (public routes will handle themselves)
        if (!$user instanceof User) {
            return true;
        }

        // Deny access if user is banned
        return $user->getBan() !== 'oui';
    }
}