<?php

namespace App\Controller;

use App\Entity\Camion_remorquage;
use App\Form\CamionRemorquageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CamionRemorquageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/camion/remorquage', name: 'app_camion_remorquage')]
    public function index(Request $request): Response
    {
        $camions = $this->entityManager->getRepository(Camion_remorquage::class)->findAll();
        $form = $this->createForm(CamionRemorquageType::class, new Camion_remorquage());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return $this->redirectToRoute('app_camion_remorquage');
        }

        return $this->render('camion_remorquage/index.html.twig', [
            'form' => $form->createView(),
            'camions' => $camions,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_camion_remorquage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Camion_remorquage $camion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CamionRemorquageType::class, $camion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Camion updated successfully');
                return $this->redirectToRoute('app_camion_remorquage');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error updating camion: ' . $e->getMessage());
            }
        }

        return $this->render('camion_remorquage/edit.html.twig', [
            'form' => $form->createView(),
            'camion' => $camion,
        ]);
    }

    #[Route('/camion/remorquage/delete/{id}', name: 'app_camion_remorquage_delete')]
    public function delete(Camion_remorquage $camion): Response
    {
        $this->entityManager->remove($camion);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_camion_remorquage');
    }
}