<?php

namespace App\Controller;

use App\Entity\Res_remorquage;
use App\Form\ResRemorquageType;
use App\Service\MailjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ReservationRemorquageController extends AbstractController
{
    private $entityManager;
    private $mailjetService;

    public function __construct(EntityManagerInterface $entityManager, MailjetService $mailjetService)
    {
        $this->entityManager = $entityManager;
        $this->mailjetService = $mailjetService;
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

        $clientEmail = $reservation->getUser()->getEmail();
        $subject = 'Confirmation de Réservation';
        $client = $reservation->getUser();
        $camion = $reservation->getCamionRemorquage();
        $dateReservation = $reservation->getDate()->format('d-m-Y');
        $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de la camion de remorquage de " . $camion->getNomAgence() . ", prévue le " . $dateReservation . ", est acceptée.";
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return $this->redirectToRoute('app_reservation_remorquage');
    }

    #[Route('/reservation/remorquage/reject/{id}', name: 'app_reservation_remorquage_reject')]
    public function reject(Res_remorquage $reservation): Response
    {
        $reservation->setStatus('rejetee');
        $this->entityManager->flush();

        $clientEmail = $reservation->getUser()->getEmail();
        $subject = 'Réservation Rejetée';
        $client = $reservation->getUser();
        $camion = $reservation->getCamionRemorquage();
        $dateReservation = $reservation->getDate()->format('d-m-Y');
        $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de la camion de remorquage de " . $camion->getNomAgence() . ", prévue le " . $dateReservation . ", est rejetée.";
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return $this->redirectToRoute('app_reservation_remorquage');
    }

    #[Route('/reservation/remorquage/new', name: 'app_reservation_remorquage_new')]
    public function new(Request $request): Response
    {
        $reservation = new Res_remorquage();
        $form = $this->createForm(ResRemorquageType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('reservation_remorquage/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
