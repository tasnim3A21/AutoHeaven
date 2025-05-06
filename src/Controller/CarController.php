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
    
    #[Route('/car/{id}/reserver/{user}', name: 'car_reservation', methods: ['POST'])]
    public function reserver(Request $request, EntityManagerInterface $entityManager, Voiture $voiture, User $user)
    {
        // Vérification de la disponibilité
        if ($voiture->getDisponibilite() !== 'oui') {
            $this->addFlash('danger', 'Cette voiture n\'est plus disponible pour un test drive.');
            return $this->redirectToRoute('app_car');
        }
        $date = $request->request->get('date');
        if (!$date) {
            $this->addFlash('danger', 'Veuillez choisir une date.');
            return $this->redirectToRoute('app_car');
        }
        $reservation = new \App\Entity\Res_testdrive();
        $reservation->setUser($user);
        $reservation->setVoiture($voiture);
        $reservation->setDate(new \DateTime($date));
        $reservation->setStatus('en_cours_de_traitement');
        $entityManager->persist($reservation);
        $entityManager->flush();
        $this->addFlash('success', 'Votre demande de test drive a été enregistrée !');
        return $this->redirectToRoute('app_car');
    }
}
