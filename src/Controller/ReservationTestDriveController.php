<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationTestDriveController extends AbstractController
{
    #[Route('/reservation/test/drive', name: 'app_reservation_test_drive')]
    public function index(): Response
    {
        return $this->render('reservation_test_drive/index.html.twig', [
            'controller_name' => 'ReservationTestDriveController',
        ]);
    }
}
