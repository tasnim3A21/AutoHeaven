<?php

namespace App\Controller;

use App\Entity\Res_testdrive;
use App\Form\ResTestDriveType;
use Doctrine\ORM\EntityManagerInterface;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailjetService;

final class ReservationTestDriveController extends AbstractController
{
    private $entityManager;
    private $mailjetService;

    public function __construct(EntityManagerInterface $entityManager, MailjetService $mailjetService)
    {
        $this->entityManager = $entityManager;
        $this->mailjetService = $mailjetService;
    }

    #[Route('/reservation/test/drive', name: 'app_reservation_test_drive')]
    public function index(Request $request): Response
    {
        $reservations = $this->entityManager->getRepository(Res_testdrive::class)->findAll();
        $reservation = new Res_testdrive();
        $form = $this->createForm(ResTestDriveType::class, $reservation);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_reservation_test_drive');
        }

        return $this->render('reservation_test_drive/index.html.twig', [
            'reservations' => $reservations,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/test/drive/edit/{id}', name: 'app_reservation_test_drive_edit')]
    public function edit(Request $request, Res_testdrive $reservation): Response
    {
        $form = $this->createForm(ResTestDriveType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->flush();
            return $this->redirectToRoute('app_reservation_test_drive');
        }

        return $this->render('reservation_test_drive/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/test/drive/delete/{id}', name: 'app_reservation_test_drive_delete', methods: ['GET'])]
    public function delete(Res_testdrive $reservation): Response
    {
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservation_test_drive');
    }

    #[Route('/reservation/test/drive/confirm/{id}', name: 'app_reservation_test_drive_confirm')]
    public function confirm(Res_testdrive $reservation): Response
    {
        $reservation->setStatus('confirmee');
        $this->entityManager->flush();

        $clientEmail = $reservation->getUser()->getEmail();
        $subject = 'Réservation Test Drive';
        $client = $reservation->getUser();
        $voiture = $reservation->getVoiture();
        $dateReservation = $reservation->getDate()->format('d-m-Y');
        $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de test drive de notre voiture " . $voiture->getMarque() . ", prévue le " . $dateReservation . ", est acceptée.";
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return $this->redirectToRoute('app_reservation_test_drive');
    }

    #[Route('/reservation/test/drive/reject/{id}', name: 'app_reservation_test_drive_reject')]
    public function reject(Res_testdrive $reservation): Response
    {
        $reservation->setStatus('rejetee');
        $this->entityManager->flush();

        $clientEmail = $reservation->getUser()->getEmail();
        $subject = 'Réservation Test Drive';
        $client = $reservation->getUser();
        $voiture = $reservation->getVoiture();
        $dateReservation = $reservation->getDate()->format('d-m-Y');
        $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de test drive de notre voiture " . $voiture->getMarque() . ", prévue le " . $dateReservation . ", est rejetée.";
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return $this->redirectToRoute('app_reservation_test_drive');
    }
}
