<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TechniciansController extends AbstractController
{
    #[Route('/technicians', name: 'app_technicians')]
    public function index(EntityManagerInterface $em): Response
    {
        // Fetch users with role 'mecanicien'
        $technicians = $em->getRepository(User::class)->findBy(['role' => 'mecanicien']);

        return $this->render('technicians/index.html.twig', [
            'controller_name' => 'TechniciansController',
            'technicians' => $technicians,
        ]);
    }
}