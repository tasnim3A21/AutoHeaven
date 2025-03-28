<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CamionRemorquageController extends AbstractController
{
    #[Route('/camion/remorquage', name: 'app_camion_remorquage')]
    public function index(): Response
    {
        return $this->render('camion_remorquage/index.html.twig', [
            'controller_name' => 'CamionRemorquageController',
        ]);
    }
}
