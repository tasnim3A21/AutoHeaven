<?php

namespace App\Controller;

use App\Entity\Res_testdrive;
use App\Entity\Res_remorquage;
use App\Entity\Res_mecanicien;
use App\Entity\User;
use App\Entity\Voiture;
use App\Entity\Camion_remorquage;
use App\Entity\Mecanicien;
use App\Form\ResTestDriveType; // Make sure this is at the top

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/booking', name: 'app_booking')]
    public function index(): Response
    {

        $userId = 3;

        // Fetch test drives with vehicle information
        $testDrives = $this->entityManager->getRepository(Res_testdrive::class)
            ->findBy(['id_u' => $userId], ['date' => 'DESC']);

        // Fetch towing requests with agency information
        $remorquages = $this->entityManager->createQuery(
            'SELECT r, cr 
            FROM App\Entity\Res_remorquage r 
            JOIN r.camionRemorquage cr 
            WHERE r.id_u = :userId 
            ORDER BY r.date DESC'
        )->setParameter('userId', $userId)->getResult();

        // Fetch mechanic appointments with mechanic information
        $mecaniciens = $this->entityManager->createQuery(
            'SELECT r, m 
            FROM App\Entity\Res_mecanicien r 
            JOIN r.mecanicien m 
            WHERE r.id_u = :userId'
        )->setParameter('userId', $userId)->getResult();

        return $this->render('booking/index.html.twig', [
            'testDrives' => $testDrives,
            'remorquages' => $remorquages,
            'mecaniciens' => $mecaniciens,
        ]);
    }


    // --- Test Drive Edit ---
    #[Route('/testdrive/{id}/edit', name: 'testdrive_edit')]
    public function editTestDrive(int $id, Request $request): Response
    {
        $testDrive = $this->entityManager->getRepository(Res_testdrive::class)->find($id);
        if (!$testDrive) {
            $this->addFlash('error', 'Réservation Test Drive introuvable.');
            return $this->redirectToRoute('app_booking');
        }

        $form = $this->createForm(ResTestDriveType::class, $testDrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Réservation Test Drive mise à jour avec succès.');
            return $this->redirectToRoute('app_booking');
        }

        return $this->render('booking/edit_testdrive.html.twig', [
            'testDrive' => $testDrive,
            'form' => $form->createView(),
        ]);
    }
    // --- Test Drive Delete ---
    #[Route('/testdrive/{id}/delete', name: 'testdrive_delete')]
    public function deleteTestDrive(int $id): Response
    {
        $testDrive = $this->entityManager->getRepository(Res_testdrive::class)->find($id);
        if ($testDrive) {
            $this->entityManager->remove($testDrive);
            $this->entityManager->flush();
            $this->addFlash('success', 'Réservation Test Drive supprimée.');
        } else {
            $this->addFlash('error', 'Réservation Test Drive introuvable.');
        }
        return $this->redirectToRoute('app_booking');
    }


    // --- Remorquage Edit ---
    #[Route('/remorquage/{id}/edit', name: 'remorquage_edit')]
    public function editRemorquage(int $id, Request $request): Response
    {
        $remorquage = $this->entityManager->getRepository(Res_remorquage::class)->find($id);
        if (!$remorquage) {
            $this->addFlash('error', 'Demande de remorquage introuvable.');
            return $this->redirectToRoute('app_booking');
        }

        $form = $this->createForm(\App\Form\ResRemorquageType::class, $remorquage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Demande de remorquage mise à jour avec succès.');
            return $this->redirectToRoute('app_booking');
        }

        return $this->render('booking/edit_rem.html.twig', [
            'remorquage' => $remorquage,
            'form' => $form->createView(),
        ]);
    }
    // --- Remorquage Delete ---
    #[Route('/remorquage/{id}/delete', name: 'remorquage_delete')]
    public function deleteRemorquage(int $id): Response
    {
        $remorquage = $this->entityManager->getRepository(Res_remorquage::class)->find($id);
        if ($remorquage) {
            $this->entityManager->remove($remorquage);
            $this->entityManager->flush();
            $this->addFlash('success', 'Demande de remorquage supprimée.');
        } else {
            $this->addFlash('error', 'Demande de remorquage introuvable.');
        }
        return $this->redirectToRoute('app_booking');
    }


    // --- Mécanicien Edit ---
    #[Route('/mecanicien/{id}/edit', name: 'mecanicien_edit')]
    public function editMecanicien(int $id, Request $request): Response
    {
        $mecanicien = $this->entityManager->getRepository(Res_mecanicien::class)->find($id);
        if (!$mecanicien) {
            $this->addFlash('error', 'Rendez-vous mécanicien introuvable.');
            return $this->redirectToRoute('app_booking');
        }

        $form = $this->createForm(\App\Form\ResMecanicienType::class, $mecanicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Rendez-vous mécanicien mis à jour avec succès.');
            return $this->redirectToRoute('app_booking');
        }

        return $this->render('booking/edit_mec.html.twig', [
            'mecanicien' => $mecanicien,
            'form' => $form->createView(),
        ]);
    }
    // --- Mécanicien Delete ---
    #[Route('/mecanicien/{id}/delete', name: 'mecanicien_delete')]
    public function deleteMecanicien(int $id): Response
    {
        $mecanicien = $this->entityManager->getRepository(Res_mecanicien::class)->find($id);
        if ($mecanicien) {
            $this->entityManager->remove($mecanicien);
            $this->entityManager->flush();
            $this->addFlash('success', 'Rendez-vous mécanicien supprimé.');
        } else {
            $this->addFlash('error', 'Rendez-vous mécanicien introuvable.');
        }
        return $this->redirectToRoute('app_booking');
    }

    #[Route('/api/reservations', name: 'api_reservations')]
    public function getReservations(ReservationRepository $repo): JsonResponse
    {
        $reservations = $repo->findAll();

        $events = [];
        foreach ($reservations as $r) {
            $events[] = [
                'title' => 'RDV avec ' . $r->getMecanicien()->getNom(),
                'start' => $r->getDate()->format('Y-m-d H:i:s'),
                // tu peux ajouter 'end', 'color', etc.
            ];
        }
        return new JsonResponse($events);
    }
}
