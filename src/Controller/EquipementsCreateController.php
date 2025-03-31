<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EquipementsCreateController extends AbstractController
{
    #[Route('/equipements/create', name: 'app_equipements_create')]
    public function index(): Response
    {
        return $this->render('equipements_create/index.html.twig', [
            'controller_name' => 'EquipementsCreateController',
        ]);
    }
}
