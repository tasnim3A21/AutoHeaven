<?php

namespace App\Controller;

use App\Entity\Res_remorquage;
use App\Form\ResRemorquageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ReservationRemorquageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reservation/remorquage', name: 'app_reservation_remorquage')]
    public function index(Request $request): Response
    {
        $reservations = $this->entityManager->getRepository(Res_remorquage::class)->findAll();
        $reservation = new Res_remorquage();
        $form = $this->createForm(ResRemorquageType::class, $reservation);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_reservation_remorquage');
        }

        return $this->render('reservation_remorquage/index.html.twig', [
            'reservations' => $reservations,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/remorquage/edit/{id}', name: 'app_reservation_remorquage_edit')]
    public function edit(Request $request, Res_remorquage $reservation): Response
    {
        $form = $this->createForm(ResRemorquageType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->flush();
            return $this->redirectToRoute('app_reservation_remorquage');
        }

        return $this->render('reservation_remorquage/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/remorquage/delete/{id}', name: 'app_reservation_remorquage_delete', methods: ['GET'])]
    public function delete(Res_remorquage $reservation): Response
    {
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservation_remorquage');
    }

    #[Route('/reservation/remorquage/confirm/{id}', name: 'app_reservation_remorquage_confirm')]
    public function confirm(Res_remorquage $reservation): Response
    {
        $reservation->setStatus('confirmee');
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservation_remorquage');
    }

    #[Route('/reservation/remorquage/reject/{id}', name: 'app_reservation_remorquage_reject')]
    public function reject(Res_remorquage $reservation): Response
    {
        $reservation->setStatus('rejetee');
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservation_remorquage');
    }
}
