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

        // Ajouter un code de coupon pour chaque offre
        $offresWithCoupons = array_map(function ($offre) {
            // Générer un code de coupon simple (ex. : OFFRE-123-REDUC20)
            $couponCode = sprintf(
                'OFFRE-%s-%s',
                $offre->getIdOffre(),
                strtoupper(substr(str_replace(' ', '', $offre->getTypeOffre()), 0, 5)) . round($offre->getTauxReduction())
            );
            return [
                'offre' => $offre,
                'couponCode' => $couponCode
            ];
        }, $offres);

        return $this->render('offres/index.html.twig', [
            'offresWithCoupons' => $offresWithCoupons,
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
