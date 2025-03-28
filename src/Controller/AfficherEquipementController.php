<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AfficherEquipementController extends AbstractController
{
    #[Route('/afficher/equipement', name: 'app_afficher_equipement')]
    public function index(): Response
    {
        return $this->render('afficher_equipement/index.html.twig', [
            'controller_name' => 'AfficherEquipementController',
        ]);
    }
}
