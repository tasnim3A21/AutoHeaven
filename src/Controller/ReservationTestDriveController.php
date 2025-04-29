<?php

namespace App\Controller;

use App\Entity\Res_testdrive;
use App\Form\ResTestDriveType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ReservationTestDriveController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

        return $this->redirectToRoute('app_reservation_test_drive');
    }

    #[Route('/reservation/test/drive/reject/{id}', name: 'app_reservation_test_drive_reject')]
    public function reject(Res_testdrive $reservation): Response
    {
        $reservation->setStatus('rejetee');
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservation_test_drive');
    }
}
