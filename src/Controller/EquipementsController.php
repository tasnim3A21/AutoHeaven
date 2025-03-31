<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EquipementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Equipement;
use App\Entity\Stock;


final class EquipementsController extends AbstractController
{
    #[Route('/equipements', name: 'app_equipements')]
    public function index(EquipementRepository $equipementRepository , EntityManagerInterface $entityManager): Response
    {
         // Récupérer tous les équipements
         $equipements = $entityManager->getRepository(Equipement::class)->findAll();

         // Récupérer tous les stocks associés
         $stocks = $entityManager->getRepository(Stock::class)->findAll();
 
         return $this->render('equipements/index.html.twig', [
             'equipements' => $equipements,
             'stocks' => $stocks,
         ]);
    }
    #[Route('/equipements/search', name: 'app_equipements_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $searchTerm = $request->query->get('search', '');
        
        $equipementRepository = $entityManager->getRepository(Equipement::class);
        $queryBuilder = $equipementRepository->createQueryBuilder('e')
            ->leftJoin(Stock::class, 's', 'WITH', 's.id = e.id');
        
        if (!empty($searchTerm)) {
            $queryBuilder
                ->where('e.nom LIKE :searchTerm OR e.marque LIKE :searchTerm OR e.reference LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }
        
        $equipements = $queryBuilder->getQuery()->getResult();
    
        $data = [];
        foreach ($equipements as $equipement) {
            $stock = $entityManager->getRepository(Stock::class)->findOneBy(['id' => $equipement]);
            
            $data[] = [
                'reference' => $equipement->getReference(),
                'nom' => $equipement->getNom(),
                'marque' => $equipement->getMarque(),
                'prix' => $stock ? $stock->getPrixvente() : 'N/A',
                'quantite' => $stock ? $stock->getQuantite() : 'N/A',
              
            ];
        }
    
        return new JsonResponse(['data' => $data]);
    }

    #[Route('/equipements/delete/{id}', name: 'app_equipements_delete', methods: ['DELETE'])]
    public function delete(Equipement $equipement, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Delete associated stock first
            $stock = $entityManager->getRepository(Stock::class)->findOneBy(['equipement' => $equipement]);
            if ($stock) {
                $entityManager->remove($stock);
            }
            
            $entityManager->remove($equipement);
            $entityManager->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Équipement supprimé avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }

    #[Route('/equipements/edit/{id}', name: 'app_equipements_edit', methods: ['GET'])]
    public function editForm(Equipement $equipement): Response
    {
        return $this->render('equipements/edit.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/equipements/update/{id}', name: 'app_equipements_update', methods: ['POST'])]
    public function update(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Équipement mis à jour avec succès');
            return $this->redirectToRoute('app_equipements');
        }

        return $this->render('equipements/edit.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }
  
}