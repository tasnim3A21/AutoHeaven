<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Voiture;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;


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
    #[Route('/car/{id}/reserver', name: 'car_reservation', methods: ['GET', 'POST'])]
    public function reserver(Voiture $voiture, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$voiture) {
            return new Response('Car not found', 404);
        }

        if ($request->isMethod('POST')) {
            $reservation = new Reservation();
            $voiture = $entityManager->getRepository(Voiture::class)->find($request->request->get('id_v'));
            $reservation->setId_v($voiture->getId_v());
            $connectedUser = $this->getUser();
            $reservation->setId($connectedUser->getUserIdentifier());
            $reservation->setDate_res(new \DateTime($request->request->get('reservation_date')));

            $entityManager->persist($reservation);
            $entityManager->flush();

            // Return a JSON response indicating success
            return $this->json(['status' => 'success', 'message' => 'Reservation successful']);
        }
    }
}
