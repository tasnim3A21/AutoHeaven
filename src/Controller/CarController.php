<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Voiture;


final class CarController extends AbstractController
{
    #[Route('/car', name: 'app_car')]
    public function index(VoitureRepository $voitureRepository): Response
    {
        $voitures = $voitureRepository->findAll();

        return $this->render('car/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }
    #[Route('/car/{id}/details', name: 'car_details', methods: ['GET'])]
    public function details(Voiture $voiture): Response
{
    if (!$voiture) {
        return new Response('Car not found', 404);
    }

    dump($voiture);  // Ensure the voiture data is being passed correctly
    return $this->render('car/details.html.twig', [
        'voiture' => $voiture,
    ]);
}
}
