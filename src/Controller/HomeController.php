<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Fetch users with role 'mecanicien'
        $technicians = $em->getRepository(User::class)->findBy(['role' => 'mecanicien']);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'technicians' => $technicians,
        ]);
    }
}