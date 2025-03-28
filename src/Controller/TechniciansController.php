<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TechniciansController extends AbstractController
{
    #[Route('/technicians', name: 'app_technicians')]
    public function index(): Response
    {
        return $this->render('technicians/index.html.twig', [
            'controller_name' => 'TechniciansController',
        ]);
    }
}
