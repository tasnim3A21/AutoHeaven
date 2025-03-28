<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListEquipementController extends AbstractController
{
    #[Route('/list/equipement', name: 'app_list_equipement')]
    public function index(): Response
    {
        return $this->render('list_equipement/index.html.twig', [
            'controller_name' => 'ListEquipementController',
        ]);
    }
}
