<?php


namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use App\Entity\User;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        /** @var User $user */
        $user = $token->getUser();

        $role = $user->getRole(); // assuming getRole() returns 'admin', 'client', or 'mecanicien'

        switch ($role) {
            case 'admin':
                return new RedirectResponse($this->router->generate('app_acceuil'));
            case 'client':
            case 'mecanicien':
                return new RedirectResponse($this->router->generate('app_home'));
            default:
                return new RedirectResponse($this->router->generate('app_login')); // fallback
        }
    }
}
