<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialUserProvider implements OAuthAwareUserProviderInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): ?UserInterface
{
    $email = $response->getEmail();
    $resourceOwner = $response->getResourceOwner()->getName();
    $oauthId = $response->getUsername();

    if (!$email) {
        $email = $oauthId . '@' . $resourceOwner . '.default';
    }

    $user = $this->userRepository->findOneBy(['email' => $email]);
    if (!$user) {
        throw new \RuntimeException('Aucun compte trouvé avec cet e-mail. Veuillez vous inscrire d\'abord.');
    }

    if ($user->getBan() === 'oui') {
        throw new \RuntimeException('Vous êtes définitivement banni du site.');
    }

    return $user;
}
}