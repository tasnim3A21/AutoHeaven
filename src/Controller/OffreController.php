<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;  // Make sure this is imported
use App\Entity\Offre;
use App\Form\OffreType;
use App\Form\OffreType2;
use App\Repository\EquipementRepository;

final class OffreController extends AbstractController
{
    #[Route('/offre', name: 'app_offre')]
    public function afficherOffres(EntityManagerInterface $em): Response
    {
        $offres = $em->getRepository(Offre::class)->findAll();
        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/get-equipement-image/{id}', name: 'get_equipement_image')]
    public function getEquipementImage(EquipementRepository $equipementRepository, $id): JsonResponse
    {
        $equipement = $equipementRepository->find($id);
        if (!$equipement) {
            return new JsonResponse(['image' => ''], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['image' => $equipement->getImage()]);
    }

    #[Route('/offre/ajouter', name: 'ajouter_offre')]
    public function ajouterOffre(Request $request, EntityManagerInterface $entityManager, EquipementRepository $equipementRepository): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateDebut = $offre->getDateDebut();
            $dateFin = $offre->getDateFin();

            if ($dateFin < $dateDebut) {
                $form->get('date_fin')->addError(new \Symfony\Component\Form\FormError(
                    'La date de fin ne peut pas être inférieure à la date de début.'
                ));
                return $this->render('offre/ajouter.html.twig', [
                    'form' => $form->createView(),
                    'equipements' => $equipementRepository->findAll(),
                ]);
            }

            $equipement = $offre->getEquipement();
            if ($equipement) {
                $offre->setImage($equipement->getImage());
            }

            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre');
        }

        return $this->render('offre/ajouter.html.twig', [
            'form' => $form->createView(),
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    #[Route('/offre/modifier/{id}', name: 'modifier_offre')]
    public function modifierOffre(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType2::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($offre->getDateFin() < $offre->getDateDebut()) {
                $form->get('date_fin')->addError(new \Symfony\Component\Form\FormError('La date de fin ne peut pas être inférieure à la date de début.'));
                return $this->render('offre/modifier.html.twig', [
                    'form' => $form->createView(),
                    'offre' => $offre,
                ]);
            }

            $entityManager->flush();
            $this->addFlash('success', 'L\'offre a été modifiée avec succès!');
            return $this->redirectToRoute('app_offre');
        }

        return $this->render('offre/modifier.html.twig', [
            'form' => $form->createView(),
            'offre' => $offre,
        ]);
    }

    #[Route('/offre/supprimer/{id}', name: 'supprimer_offre')]
    public function supprimerOffre(Offre $offre, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($offre);
        $entityManager->flush();

        $this->addFlash('success', 'L\'offre a été supprimée avec succès!');
        return $this->redirectToRoute('app_offre');  // Correct RedirectResponse
    }
}
