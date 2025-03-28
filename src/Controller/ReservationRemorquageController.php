<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationRemorquageController extends AbstractController
{
    #[Route('/reservation/remorquage', name: 'app_reservation_remorquage')]
    public function index(): Response
    {
        return $this->render('reservation_remorquage/index.html.twig', [
            'controller_name' => 'ReservationRemorquageController',
        ]);
    }
}
