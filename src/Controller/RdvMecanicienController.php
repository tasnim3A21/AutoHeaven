<?php

namespace App\Controller;

use App\Entity\Res_mecanicien;
use App\Form\ResMecanicienType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RdvMecanicienController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/rdv/mecanicien', name: 'app_rdv_mecanicien')]
    public function index(): Response
    {
        $reservations = $this->entityManager->getRepository(Res_mecanicien::class)
            ->createQueryBuilder('r')
            ->select('r, client, mec')
            ->leftJoin('r.client', 'client')
            ->leftJoin('r.mecanicien', 'mec')
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('rdv_mecanicien/index.html.twig', [
            'reservations' => $reservations
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

        return $this->redirectToRoute('app_rdv_mecanicien');
    }

    #[Route('/rdv/mecanicien/reject/{id}', name: 'app_rdv_mecanicien_reject')]
    public function reject(Res_mecanicien $reservation): Response
    {
        $reservation->setStatus('rejetee');
        $this->entityManager->flush();

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

            return $this->redirectToRoute('app_home');
        }

        return $this->render('rdv_mecanicien/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
