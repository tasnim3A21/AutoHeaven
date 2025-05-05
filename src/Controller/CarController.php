<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Voiture;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Res_testdrive;
use App\Entity\User;


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
    
    #[Route('/car/{id}/reserver/{user}', name: 'car_reservation', methods: ['GET', 'POST'])]
    public function reserver(Request $request, EntityManagerInterface $entityManager): Response
    {
        $idV = $request->request->get('id_v');
        $clientId = $request->request->get('client_id');
        $reservationDate = $request->request->get('reservation_date');

        $voiture = $entityManager->getRepository(Voiture::class)->find($idV);
        $user = $entityManager->getRepository(User::class)->find($clientId);

        if (!$voiture || !$user) {
            return new Response('Car or User not found', 404);
        }

        $reservation = new Res_testdrive();
        $reservation->setIdV($voiture->getIdV());
        $reservation->setIdU($user->getId());
        $reservation->setDate(new \DateTime($reservationDate));

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('car_details', ['id' => $voiture->getIdV()]);
    }
}
