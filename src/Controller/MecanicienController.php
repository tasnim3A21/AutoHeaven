<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MecanicienController extends AbstractController
{
    #[Route('/mecanicien', name: 'app_mecanicien')]
    public function index(): Response
    {
        return $this->render('mecanicien/index.html.twig', [
            'controller_name' => 'MecanicienController',
        ]);
    }
}
