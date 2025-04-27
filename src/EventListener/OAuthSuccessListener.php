<?php

namespace App\EventListener;

use HWI\Bundle\OAuthBundle\Event\FilterUserResponseEvent;
use HWI\Bundle\OAuthBundle\HWIOAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OAuthSuccessListener implements EventSubscriberInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HWIOAuthEvents::CONNECT_COMPLETED => 'onConnectCompleted',
        ];
    }

    public function onConnectCompleted(FilterUserResponseEvent $event): void
    {
        $user = $event->getUser();
        $roles = $user->getRoles();

        // Ajout de débogage pour voir les rôles
        file_put_contents(__DIR__ . '/roles_debug.log', 'User Roles: ' . json_encode($roles) . PHP_EOL, FILE_APPEND);

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_acceuil')));
        } else {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
        }
    }
}