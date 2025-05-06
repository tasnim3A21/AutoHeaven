<?php

namespace App\Controller;

use App\Entity\Res_mecanicien;
use App\Entity\User;
use App\Form\ResMecanicienType;
use App\Service\MailjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class RdvMecanicienController extends AbstractController
{
    private $entityManager;


    private $mailjetService;

    public function __construct(EntityManagerInterface $entityManager, MailjetService $mailjetService)
    {
        $this->entityManager = $entityManager;
        $this->mailjetService = $mailjetService;
    }

    #[Route('/rdv/mecanicien', name: 'app_rdv_mecanicien')]
    public function index(Request $request): Response
    {
        $reservations = $this->entityManager->getRepository(Res_mecanicien::class)->findAll();
        
        $newReservation = new Res_mecanicien();
        $form = $this->createForm(ResMecanicienType::class, $newReservation);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newReservation->setStatus('en_cours_de_traitement');
            $this->entityManager->persist($newReservation);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Nouveau rendez-vous créé avec succès.');
            return $this->redirectToRoute('app_rdv_mecanicien'); 
        }

        return $this->render('rdv_mecanicien/index.html.twig', [
            'reservations' => $reservations,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rdv/mecanicien/edit/{id}', name: 'app_rdv_mecanicien_edit')]
    public function edit(Request $request, Res_mecanicien $reservation): Response
    {
        $form = $this->createForm(ResMecanicienType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_rdv_mecanicien');
        }

        return $this->render('rdv_mecanicien/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rdv/mecanicien/delete/{id}', name: 'app_rdv_mecanicien_delete', methods: ['GET'])]
    public function delete(Res_mecanicien $reservation): Response
    {
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_rdv_mecanicien');
    }

    #[Route('/rdv/mecanicien/confirm/{id}', name: 'app_rdv_mecanicien_confirm')]
    public function confirm(Res_mecanicien $reservation): Response
    {
        $reservation->setStatus('confirmee');
        $this->entityManager->flush();

        $clientEmail = $reservation->getClient()->getEmail();
        $subject = 'Reservation RDV Mecanicien';
        $client = $reservation->getClient();
        $mecanicien = $reservation->getMecanicien();
        $dateReservation = $reservation->getDate()->format('d-m-Y');
        $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de rendez-vous avec notre mécanicien " . $mecanicien->getNom() . " " . $mecanicien->getPrenom() . ", prévue le " . $dateReservation . ", est acceptée.";
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return $this->redirectToRoute('app_rdv_mecanicien');
    }

    #[Route('/rdv/mecanicien/reject/{id}', name: 'app_rdv_mecanicien_reject')]
    public function reject(Res_mecanicien $reservation): Response
    {
        $reservation->setStatus('rejetee');
        $this->entityManager->flush();

        $clientEmail = $reservation->getClient()->getEmail();
        $subject = 'Reservation RDV Mecanicien';
        $client = $reservation->getClient();
        $mecanicien = $reservation->getMecanicien();
        $dateReservation = $reservation->getDate()->format('d-m-Y');
        $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de rendez-vous avec notre mécanicien " . $mecanicien->getNom() . " " . $mecanicien->getPrenom() . ", prévue le " . $dateReservation . ", est rejetée.";
        
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return $this->redirectToRoute('app_rdv_mecanicien');
    }

    #[Route('/rdv/mecanicien/new', name: 'app_rdv_mecanicien_new')]
    public function new(Request $request): Response
    {
        $reservation = new Res_mecanicien();
        $form = $this->createForm(ResMecanicienType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setStatus('en_cours_de_traitement');
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_rdv_mecanicien'); 
        }

        return $this->render('rdv_mecanicien/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rdv/mecanicien/reserver/{id_mec}', name: 'app_rdv_mecanicien_reserver', methods: ['POST'])]
    public function reserver(Request $request, $id_mec): Response
    {
        $reservation = new Res_mecanicien();
        $mecanicien = $this->entityManager->getRepository(User::class)->find($id_mec);
        if (!$mecanicien) {
            return new Response('Mécanicien non trouvé', Response::HTTP_NOT_FOUND);
        }
        $reservation->setMecanicien($mecanicien);
        $reservation->setStatus('en_cours_de_traitement');
        // Associer le client connecté à la réservation
        $client = $this->getUser();
        if (!$client) {
            return new Response('Client non authentifié', Response::HTTP_FORBIDDEN);
        }
        $reservation->setClient($client);
        // Récupérer la note depuis la requête
        $note = $request->request->get('note');
        if (!$note) {
            return new Response('Note requise', Response::HTTP_BAD_REQUEST);
        }
        $reservation->setNote($note);
        // Récupérer l'adresse depuis la requête
        $adresse = $request->request->get('adresse');
        if (!$adresse) {
            return new Response('Adresse requise', Response::HTTP_BAD_REQUEST);
        }
        $reservation->setAdresse($adresse);
        // Set the reservation date from POST data
        $dateString = $request->request->get('date');
        if (!$dateString) {
            return new Response('Date requise', Response::HTTP_BAD_REQUEST);
        }
        try {
            $date = new \DateTime($dateString);
        } catch (\Exception $e) {
            return new Response('Date invalide', Response::HTTP_BAD_REQUEST);
        }
        $reservation->setDate($date);
        // Ici, vous pouvez ajouter d'autres champs nécessaires à la réservation (date, etc.)
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
        $this->addFlash('success', 'Réservation effectuée avec succès.');
        return $this->redirectToRoute('app_technicians');
    }

    #[Route('/send-email/{reservationId}/{action}', name: 'app_send_email')]
    public function sendEmail($reservationId, $action): Response {
        $reservation = $this->entityManager->getRepository(Res_mecanicien::class)->find($reservationId);
        if (!$reservation) {
            return new Response('Reservation not found', Response::HTTP_NOT_FOUND);
        }

        $clientEmail = $reservation->getClient()->getEmail();
        $client = $reservation->getClient();
        $mecanicien = $reservation->getMecanicien();
        $dateReservation = $reservation->getDate()->format('d-m-Y');

        if ($action === 'confirm') {
            $reservation->setStatus('confirmee');
            $subject = 'Reservation Confirmer';
            $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de rendez-vous avec notre mécanicien " . $mecanicien->getNom() . " " . $mecanicien->getPrenom() . ", prévue le " . $dateReservation . ", est acceptée.";
        } elseif ($action === 'reject') {
            $reservation->setStatus('rejetee');
            $subject = 'Reservation Rejecter';
            $text = "Salut, notre cher client " . $client->getNom() . " " . $client->getPrenom() . ", votre réservation de rendez-vous avec notre mécanicien " . $mecanicien->getNom() . " " . $mecanicien->getPrenom() . ", prévue le " . $dateReservation . ", est rejetée.";
        } else {
            return new Response('Invalid action', Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();
        $this->mailjetService->sendEmail('rayen.elfil@esprit.tn', 'AutoHeaven CEO', $clientEmail, $subject, $text);

        return new Response('Email sent successfully');
    }
}
