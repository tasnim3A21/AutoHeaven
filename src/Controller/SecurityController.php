<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    
   
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, EntityManagerInterface $em): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        // Check if the user is authenticated
        if ($this->getUser()) {
            // ⚠ Re-fetch the user from the database to get the updated entity
            $sessionUser = $this->getUser();
            $user = $em->getRepository(\App\Entity\User::class)->find($sessionUser->getId());
    
            // BAN CHECK
            $ban = $user->getBan();
            if ($ban !== null && str_starts_with($ban, 'oui:')) {
                $banTime = substr($ban, 4);
                $bannedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $banTime);
    
                if ($bannedAt) {
                    $now = new \DateTime();
                    $diff = $now->getTimestamp() - $bannedAt->getTimestamp();
    
                    if ($diff < 300) { // Still banned
                        $remaining = 300 - $diff;
                        $minutes = ceil($remaining / 60);
                        return $this->render('login/index.html.twig', [
                            'last_username' => $lastUsername,
                            'error' => new \Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException(
                                "Vous êtes temporairement banni. Réessayez dans environ $minutes minute(s)."
                            ),
                        ]);
                    } else {
                        // Unban the user after 5 minutes
                        $user->setBan('non');
                        $em->flush(); // Persist change
                    }
                }
            }
    
            // Redirection par rôle
            $role = $user->getRoles()[0];
            if ($role === 'admin') {
                return $this->redirectToRoute('app_acceuil');
            } elseif ($role === 'client' || $role === 'mecanicien') {
                return $this->redirectToRoute('app_home');
            }
        }
    
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    
#[Route(path: '/logout', name: 'app_logout')]
public function logout(): void
{
    // Controller can be blank: it will be intercepted by the logout key on your firewall
    throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall.');
}

   
}