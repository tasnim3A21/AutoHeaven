<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface; // Ajouter cette ligne
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\OffreType;
use App\Form\OffreType2;
use App\Repository\EquipementRepository;


use App\Entity\Offre;


final class OffresController extends AbstractController
{
    #[Route('/offres', name: 'app_offres')]
    public function afficherOffres(EntityManagerInterface $em): Response
{
    // Récupérer toutes les offres avec les équipements associés
    $offres = $em->getRepository(Offre::class)->findAll();

    return $this->render('offres/index.html.twig', [
        'offres' => $offres,
    ]);
}
#[Route('/get-equipement-image/{id}', name: 'get_equipement_image')]
public function getEquipementImage(EquipementRepository $equipementRepository, $id): JsonResponse
{
    $equipement = $equipementRepository->find($id);
    if (!$equipement) {
        return new JsonResponse(['image' => ''], JsonResponse::HTTP_NOT_FOUND); // Return empty if no equipement found
    }

    return new JsonResponse(['image' => $equipement->getImage()]); // Return the image field
}



}
