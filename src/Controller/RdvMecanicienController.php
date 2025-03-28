<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RdvMecanicienController extends AbstractController
{
    #[Route('/rdv/mecanicien', name: 'app_rdv_mecanicien')]
    public function index(): Response
    {
        return $this->render('rdv_mecanicien/index.html.twig', [
            'controller_name' => 'RdvMecanicienController',
        ]);
    }
}
