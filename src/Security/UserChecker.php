<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $ban = $user->getBan();
        if ($ban !== null && str_starts_with($ban, 'oui:')) {
            $banTime = substr($ban, 4);
            $bannedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $banTime);

            if ($bannedAt) {
                $now = new \DateTime();
                $diff = $now->getTimestamp() - $bannedAt->getTimestamp();

                if ($diff < 300) {
                    $remaining = 300 - $diff;
                    $minutes = ceil($remaining / 60);

                    throw new CustomUserMessageAuthenticationException(
                        "Vous êtes temporairement banni. Réessayez dans environ $minutes minute(s)."
                    );
                }
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // not used in our case
    }
}
